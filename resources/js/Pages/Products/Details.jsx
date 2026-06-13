import { Head, Link, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import axios from "axios";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import Hr from "../../components/Hr";
import NavLinkBtn from "../../components/NavLinkBtn";
import PrimaryButton from "../../components/PrimaryButton";
import ProductSingle from "../../components/client/ProductSingle";
import RecommendedProducts from "../../components/home/RecommendedProducts";
import UserLayout from "../../Layouts/User/App";

function RatingStars({ rating = 0 }) {
    const roundedRating = Math.round(Number(rating || 0));

    return (
        <div className="flex items-center gap-0.5">
            {[1, 2, 3, 4, 5].map((star) => (
                <i
                    key={star}
                    className="fas fa-star"
                    style={{
                        color:
                            roundedRating >= star
                                ? "#fbbf24"
                                : "#d1d5db",
                    }}
                ></i>
            ))}
        </div>
    );
}

function TaskCounter({ task, product }) {
    const [taskState, setTaskState] = useState(task);
    const inFlight = useRef(false);

    useEffect(() => {
        setTaskState(task);
    }, [task]);

    useEffect(() => {
        if (
            !taskState?.enabled ||
            !taskState?.task_not_complete_yet ||
            Number(taskState?.countdown) < 1
        ) {
            return undefined;
        }

        const interval = setInterval(async () => {
            if (inFlight.current) return;

            inFlight.current = true;

            try {
                const response = await axios.post(
                    `/product/${product.id}/${product.slug}/task`,
                );

                setTaskState(response.data);
            } catch (error) {
                clearInterval(interval);
            } finally {
                inFlight.current = false;
            }
        }, 1000);

        return () => clearInterval(interval);
    }, [product.id, product.slug, taskState]);

    if (!taskState?.enabled) {
        return null;
    }

    return (
        <>
            <style>{`
                #taskPrev {
                    position: fixed;
                    bottom: 48px;
                    right: 96px;
                    min-width: 78px;
                    height: 34px;
                    padding: 0 8px;
                    border: 1px solid rgb(25, 78, 46);
                    border-radius: 25px;
                    z-index: 100000;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: #ffffff;
                    font-size: 18px;
                    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.18);
                }

                #taskPrev .badges {
                    position: absolute;
                    top: -12px;
                    left: 0px;
                    padding: 1px 5px;
                    background-color: green;
                    color: white;
                    font-size: 10px;
                    border-radius: 25px;
                }

                @media (max-width: 768px) {
                    #taskPrev {
                        right: 12px;
                        bottom: 108px;
                    }
                }
            `}</style>

            {!taskState.task_not_complete_yet ? (
                <div id="taskPrev">
                    <div className="badges">Task</div>
                    Done
                </div>
            ) : (
                <div id="taskPrev">
                    <div className="badges">
                        {taskState.countdown ?? 0} MIN
                    </div>
                    <div id="min">{taskState.min}</div>:
                    <div id="sec">{taskState.sec}</div>
                </div>
            )}
        </>
    );
}

export default function Details({
    product,
    relatedProducts = [],
    recommendedProducts = [],
    task,
}) {
    const { auth, permissions = [] } = usePage().props;
    const [visibleComments, setVisibleComments] = useState(30);
    const [forYouProducts, setForYouProducts] = useState(recommendedProducts);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editCommentText, setEditCommentText] = useState("");
    const [updatingComment, setUpdatingComment] = useState(false);
    const [hoverRating, setHoverRating] = useState(0);
    const [commentImagePreviews, setCommentImagePreviews] = useState([]);
    const [commentLikes, setCommentLikes] = useState({});
    const [commentErrors, setCommentErrors] = useState({});
    const [submittingComment, setSubmittingComment] = useState(false);
    const [commentsState, setCommentsState] = useState(product.comments ?? []);
    const { data, setData } = useForm({
        comments: "",
        rating: "",
        images: [],
        product_id: product.id,
    });

    const seoTitle = product.meta_title || product.title || product.name;
    const seoDescription = product.meta_description || product.title || "";
    const seoImage = product.meta_thumbnail || product.thumbnail;
    const shop = product.owner?.shop;
    const canDeleteComment = (comment) =>
        comment.can_delete ||
        permissions.includes("users_manage") ||
        auth?.user?.id === comment.user_id;
    const canEditComment = (comment) =>
        comment.can_edit || auth?.user?.id === comment.user_id;
    const comments = commentsState;
    const displayedComments = comments.slice(0, visibleComments);
    const hasMoreComments = visibleComments < comments.length;
    const ratedComments = comments.filter((comment) => Number(comment.rating) > 0);
    const liveRatingAverage = ratedComments.length
        ? Math.round(
              (ratedComments.reduce(
                  (total, comment) => total + Number(comment.rating || 0),
                  0,
              ) /
                  ratedComments.length) *
                  10,
          ) / 10
        : 0;
    const liveProduct = {
        ...product,
        comments,
        rating: {
            ...(product.rating ?? {}),
            average: liveRatingAverage,
            out_of_10: liveRatingAverage ? Math.round(liveRatingAverage * 2 * 10) / 10 : 0,
            count: comments.length,
        },
    };
    const imageErrorMessages = Object.entries(commentErrors)
        .filter(([key]) => key === "images" || key.startsWith("images."))
        .flatMap(([, messages]) => messages);

    useEffect(() => {
        setForYouProducts(recommendedProducts);
    }, [recommendedProducts]);

    useEffect(() => {
        setCommentsState(product.comments ?? []);
    }, [product.comments]);

    useEffect(() => {
        return () => {
            commentImagePreviews.forEach((preview) => URL.revokeObjectURL(preview.url));
        };
    }, [commentImagePreviews]);

    const displayedFormRating = hoverRating || data.rating;

    const handleCommentImages = (event) => {
        const selectedFiles = Array.from(event.target.files || []);
        const files = selectedFiles.slice(0, 3);

        commentImagePreviews.forEach((preview) => URL.revokeObjectURL(preview.url));
        setData("images", files);
        setCommentErrors((items) => {
            const nextErrors = { ...items };

            if (selectedFiles.length > 3) {
                nextErrors.images = ["You can upload maximum 3 images."];
            } else {
                delete nextErrors.images;
            }

            return nextErrors;
        });
        setCommentImagePreviews(
            files.map((file) => ({
                name: file.name,
                url: URL.createObjectURL(file),
            })),
        );
    };

    const clearCommentImages = () => {
        commentImagePreviews.forEach((preview) => URL.revokeObjectURL(preview.url));
        setData("images", []);
        setCommentImagePreviews([]);
    };

    const submitComment = (e) => {
        e.preventDefault();

        const nextErrors = {};

        if (!data.comments.trim()) {
            nextErrors.comments = ["Comment is required."];
        }

        if (!data.rating) {
            nextErrors.rating = ["Rating is required."];
        }

        if (Object.keys(nextErrors).length) {
            setCommentErrors(nextErrors);
            return;
        }

        const payload = new FormData();
        payload.append("product_id", data.product_id);
        payload.append("comments", data.comments.trim());
        payload.append("rating", data.rating);
        data.images.forEach((image) => payload.append("images[]", image));

        setSubmittingComment(true);
        setCommentErrors({});

        axios
            .post(route("user.comment.store"), payload, {
                headers: {
                    Accept: "application/json",
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                if (response.data?.comment) {
                    setCommentsState((items) => [
                        response.data.comment,
                        ...items.filter((item) => item.id !== response.data.comment.id),
                    ]);
                }

                setData({
                    comments: "",
                    rating: "",
                    images: [],
                    product_id: product.id,
                });
                setHoverRating(0);
                clearCommentImages();
            })
            .catch((error) => {
                if (error.response?.status === 422) {
                    setCommentErrors(error.response.data.errors ?? {});
                    return;
                }

                setCommentErrors({
                    comments: [error.response?.data?.message || "Comment could not be saved."],
                });
            })
            .finally(() => setSubmittingComment(false));
    };

    const deleteComment = (id) => {
        if (!confirm("Are you sure?")) return;

        router.post(
            route("user.comment.destroy", { id }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const toggleCommentLike = async (comment) => {
        if (!auth?.user) {
            router.get(route("login"));
            return;
        }

        try {
            const response = await axios.post(route("user.comment.like", { id: comment.id }));

            setCommentLikes((items) => ({
                ...items,
                [comment.id]: {
                    like: response.data.like,
                    liked: response.data.liked,
                },
            }));
        } catch (error) {
            console.error(error);
        }
    };

    const startEditComment = (comment) => {
        setEditingCommentId(comment.id);
        setEditCommentText(comment.comments ?? "");
    };

    const cancelEditComment = () => {
        setEditingCommentId(null);
        setEditCommentText("");
    };

    const submitEditComment = (id) => {
        if (!editCommentText.trim()) return;

        setUpdatingComment(true);

        router.post(
            route("user.comment.update", { id }),
            { comments: editCommentText.trim() },
            {
                preserveScroll: true,
                onSuccess: cancelEditComment,
                onFinish: () => setUpdatingComment(false),
            },
        );
    };

    return (
        <UserLayout title={seoTitle}>
            <Head>
                <meta name="title" content={product.seo_title || product.name} />
                <meta name="description" content={seoDescription} />
                <meta name="keyword" content={product.keyword || ""} />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content={seoTitle} />
                <meta
                    name="twitter:description"
                    content={String(
                        seoDescription.replace(/<[^>]*>?/gm, "").trim(),
                    )}
                />
                <meta
                    name="twitter:image"
                    content={`/storage/${seoImage || ""}`}
                />
                <meta property="og:type" content="og:product" />
                <meta property="og:title" content={seoTitle} />
                <meta
                    property="og:image"
                    content={`/storage/${seoImage || ""}`}
                />
                <meta
                    property="og:description"
                    content={String(
                        seoDescription.replace(/<[^>]*>?/gm, "").trim(),
                    )}
                />
            </Head>

            <Container>
                <ProductSingle
                    product={liveProduct}
                    relatedProduct={relatedProducts}
                    savedForLater={product.is_saved_for_later}
                    onSaveForLaterChange={(savedProduct, isSaved) => {
                        setForYouProducts((items) => {
                            const withoutProduct = items.filter(
                                (item) => item.id !== savedProduct.id,
                            );

                            return isSaved
                                ? [savedProduct, ...withoutProduct]
                                : withoutProduct;
                        });
                    }}
                />

                <SectionSection>
                    <SectionHeader
                        title="Shop Details"
                        content="this product belongs to bellow shop. see about the shop."
                    />

                    {auth?.user?.id === product.owner?.id ? (
                        <SectionInner>
                            <strong className="p-2 text-white border rounded bg-sky-900">
                                It&apos;s your product
                            </strong>
                        </SectionInner>
                    ) : (
                        <SectionInner>
                            <Hr />
                            <div className="flex flex-wrap">
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Name
                                    </div>
                                    <div className="font-bold text-md">
                                        {shop?.shop_name_en ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Owner
                                    </div>
                                    <div className="font-bold text-md">
                                        {product.owner?.name ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Location
                                    </div>
                                    <div className="font-bold text-md">
                                        {shop?.address ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Address
                                    </div>
                                    <div className="font-bold text-md">
                                        {shop?.address ?? "N/A"}
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div className="flex flex-wrap space-x-2">
                                {shop?.id ? (
                                    <NavLinkBtn
                                        href={route("shops.visit", {
                                            id: shop.id,
                                            name: shop.shop_name_en,
                                        })}
                                    >
                                        Visit Shop
                                    </NavLinkBtn>
                                ) : null}
                            </div>
                        </SectionInner>
                    )}
                </SectionSection>

                <SectionSection>
                    <SectionInner>
                        <div
                            className="w-full p-2"
                            dangerouslySetInnerHTML={{
                                __html:
                                    product.description ||
                                    "No Description Found !",
                            }}
                        />
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader title="Comments" content="" />

                    {auth?.user ? (
                        <SectionInner>
                            <form
                                onSubmit={submitComment}
                                className="w-full lg:w-2/3"
                                encType="multipart/form-data"
                            >
                                <div className="text-xs text-red-600">
                                    {commentErrors.comments?.[0]}
                                </div>
                                <div className="rounded-lg border border-orange-200 bg-white p-3 shadow-sm focus-within:border-orange-400">
                                    <textarea
                                        rows={5}
                                        className="w-full resize-y border-0 px-1 py-2 text-sm shadow-none focus:border-0 focus:ring-0"
                                        name="comments"
                                        placeholder="Write your comments"
                                        value={data.comments}
                                        onChange={(e) =>
                                            setData("comments", e.target.value)
                                        }
                                    />

                                    <div className="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                        <div className="flex items-center gap-1">
                                            {[1, 2, 3, 4, 5].map((star) => (
                                                <button
                                                    key={star}
                                                    type="button"
                                                    className="inline-flex h-8 w-8 items-center justify-center text-2xl transition hover:scale-110 focus:outline-none"
                                                    onClick={() => setData("rating", star)}
                                                    onMouseEnter={() => setHoverRating(star)}
                                                    onMouseLeave={() => setHoverRating(0)}
                                                    aria-label={`Rate ${star} out of 5`}
                                                    title={`${star} star`}
                                                >
                                                    <i
                                                        className="fas fa-star"
                                                        style={{
                                                            color:
                                                                displayedFormRating >= star
                                                                    ? "#fbbf24"
                                                                    : "#d1d5db",
                                                        }}
                                                    ></i>
                                                </button>
                                            ))}
                                            <span className="pl-2 text-xs text-slate-500">
                                                {data.rating ? `${data.rating}/5` : "Add rating"}
                                            </span>
                                        </div>

                                        <label className="inline-flex cursor-pointer items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600">
                                            <i className="fas fa-images"></i>
                                            Upload Images
                                            <input
                                                type="file"
                                                accept="image/*"
                                                multiple
                                                max="3"
                                                className="hidden"
                                                onChange={handleCommentImages}
                                            />
                                        </label>
                                    </div>

                                    {commentErrors.rating ? (
                                        <div className="mt-2 text-xs text-red-600">
                                            {commentErrors.rating[0]}
                                        </div>
                                    ) : null}
                                    {imageErrorMessages.length ? (
                                        <div className="mt-2 text-xs text-red-600">
                                            {imageErrorMessages[0]}
                                        </div>
                                    ) : null}

                                    {commentImagePreviews.length ? (
                                        <div className="mt-3 flex flex-wrap gap-2">
                                            {commentImagePreviews.map((preview) => (
                                                <img
                                                    key={preview.url}
                                                    src={preview.url}
                                                    alt={preview.name}
                                                    className="h-16 w-16 rounded-md border border-slate-200 object-cover"
                                                />
                                            ))}
                                            <button
                                                type="button"
                                                className="h-16 rounded-md border border-slate-200 px-3 text-xs text-slate-500 hover:bg-slate-50"
                                                onClick={clearCommentImages}
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    ) : null}
                                </div>

                                <div className="pt-3">
                                    <PrimaryButton disabled={submittingComment}>
                                        submit
                                    </PrimaryButton>
                                </div>
                            </form>
                        </SectionInner>
                    ) : (
                        <SectionInner>
                            <div>
                                <Link href={route("login")}>
                                    Log In to add comment
                                </Link>
                            </div>
                        </SectionInner>
                    )}

                    <Hr />

                    <SectionInner>
                        <div className="space-y-3">
                            {displayedComments.length ? (
                                displayedComments.map((item) => (
                                    <div
                                        key={item.id}
                                        className="border-b border-slate-200 bg-white px-4 py-5"
                                    >
                                        <div className="flex items-start justify-between gap-4">
                                            <div className="min-w-0 flex-1">
                                                <div className="flex items-center justify-between gap-3">
                                                    {item.rating ? (
                                                        <RatingStars rating={item.rating} />
                                                    ) : (
                                                        <span className="text-xs text-slate-400">
                                                            No rating
                                                        </span>
                                                    )}

                                                    <span className="shrink-0 text-xs text-slate-500">
                                                        {item.created_at_date ?? item.created_at_human}
                                                    </span>
                                                </div>

                                                <div className="mt-1 flex flex-wrap items-center gap-2 text-sm">
                                                    <span className="font-medium text-slate-700">
                                                        {item.user?.name ?? "User"}
                                                    </span>
                                                    {item.is_verified_purchase ? (
                                                        <span className="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                                            <i className="fas fa-check-circle"></i>
                                                            Verified Purchase
                                                        </span>
                                                    ) : null}
                                                </div>

                                                {editingCommentId === item.id ? (
                                                    <div className="mt-3">
                                                        <textarea
                                                            className="w-full min-h-[90px] resize-y rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-orange-300 focus:ring-orange-200"
                                                            value={editCommentText}
                                                            onChange={(event) =>
                                                                setEditCommentText(event.target.value)
                                                            }
                                                        />
                                                        <div className="mt-2 flex items-center gap-2">
                                                            <button
                                                                type="button"
                                                                className="rounded-md bg-orange-500 px-4 py-2 text-xs font-semibold text-white hover:bg-orange-600 disabled:opacity-60"
                                                                disabled={updatingComment || !editCommentText.trim()}
                                                                onClick={() => submitEditComment(item.id)}
                                                            >
                                                                Save
                                                            </button>
                                                            <button
                                                                type="button"
                                                                className="rounded-md border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                                                                onClick={cancelEditComment}
                                                            >
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <>
                                                        {item.comments ? (
                                                            <div className="mt-3 whitespace-pre-line text-[15px] leading-6 text-slate-950">
                                                                {item.comments}
                                                            </div>
                                                        ) : null}

                                                        {item.images?.length ? (
                                                            <div className="mt-4 flex flex-wrap gap-2">
                                                                {item.images.map((image) => (
                                                                    <img
                                                                        key={image}
                                                                        src={`/storage/${image}`}
                                                                        alt=""
                                                                        className="h-20 w-24 rounded border border-slate-200 object-cover"
                                                                    />
                                                                ))}
                                                            </div>
                                                        ) : null}

                                                        {item.variant_label ? (
                                                            <div className="mt-4 text-xs text-slate-500">
                                                                {item.variant_label}
                                                            </div>
                                                        ) : null}

                                                        <div className="mt-3 flex items-center gap-4 text-xs text-slate-500">
                                                            <button
                                                                type="button"
                                                                className={`inline-flex items-center gap-1 rounded px-1 py-0.5 transition ${
                                                                    (commentLikes[item.id]?.liked ?? item.liked_by_me)
                                                                        ? "text-orange-600"
                                                                        : "text-slate-500 hover:text-orange-600"
                                                                }`}
                                                                onClick={() => toggleCommentLike(item)}
                                                            >
                                                                <i className="fas fa-thumbs-up"></i>
                                                                {commentLikes[item.id]?.like ?? item.like ?? 0}
                                                            </button>

                                                            {canEditComment(item) ? (
                                                                <button
                                                                    type="button"
                                                                    className="font-medium text-slate-600 hover:text-orange-600"
                                                                    onClick={() => startEditComment(item)}
                                                                >
                                                                    Edit
                                                                </button>
                                                            ) : null}

                                                            {canDeleteComment(item) ? (
                                                                <button
                                                                    type="button"
                                                                    className="font-medium text-slate-600 hover:text-red-600"
                                                                    onClick={() =>
                                                                        deleteComment(item.id)
                                                                    }
                                                                >
                                                                    Delete
                                                                </button>
                                                            ) : null}
                                                        </div>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                                    No comments found.
                                </div>
                            )}
                        </div>

                        {hasMoreComments ? (
                            <div className="flex justify-center mt-5">
                                <PrimaryButton
                                    type="button"
                                    onClick={() =>
                                        setVisibleComments((count) => count + 30)
                                    }
                                >
                                    Load More
                                </PrimaryButton>
                            </div>
                        ) : null}
                    </SectionInner>
                </SectionSection>

                <RecommendedProducts products={forYouProducts} />
            </Container>

            <TaskCounter task={task} product={product} />
        </UserLayout>
    );
}
