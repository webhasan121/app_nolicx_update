import { Head, Link, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
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
    const { data, setData, post, processing, errors, reset } = useForm({
        comments: "",
        product_id: product.id,
    });

    const seoTitle = product.meta_title || product.title || product.name;
    const seoDescription = product.meta_description || product.title || "";
    const seoImage = product.meta_thumbnail || product.thumbnail;
    const shop = product.owner?.shop;
    const canDeleteComment = (comment) =>
        permissions.includes("users_manage") ||
        auth?.user?.id === comment.user_id;

    const submitComment = (e) => {
        e.preventDefault();

        post(route("user.comment.store"), {
            preserveScroll: true,
            onSuccess: () => reset("comments"),
        });
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
                    product={product}
                    relatedProduct={relatedProducts}
                />

                <SectionSection>
                    <SectionHeader
                        title="Shop Details"
                        content="this product belongs to bellow shop. see about the shop."
                    />

                    {auth?.user?.id === product.owner?.id ? (
                        <SectionInner>
                            <strong className="p-2 text-white rounded border bg-sky-900">
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
                                    <div className="text-md font-bold">
                                        {shop?.shop_name_en ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Owner
                                    </div>
                                    <div className="text-md font-bold">
                                        {product.owner?.name ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Location
                                    </div>
                                    <div className="text-md font-bold">
                                        {shop?.address ?? "N/A"}
                                    </div>
                                </div>
                                <div className="w-48 p-2 m-2 border-b">
                                    <div className="text-sm font-normal">
                                        Shop Address
                                    </div>
                                    <div className="text-md font-bold">
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

                    <SectionInner>
                        {product.comments?.map((item) => (
                            <div
                                key={item.id}
                                className="px-2 py-3 mb-1 bg-gray-100"
                            >
                                <div className="flex justify-between">
                                    <div className="text-xs">
                                        <span className="text-indigo-900">
                                            {item.user?.name}
                                        </span>{" "}
                                        at {item.created_at_human}
                                    </div>

                                    {canDeleteComment(item) ? (
                                        <button
                                            type="button"
                                            className="mt-1 text-xs bg-white border rounded"
                                            onClick={() =>
                                                deleteComment(item.id)
                                            }
                                        >
                                            <i className="fas fa-trash"></i>
                                        </button>
                                    ) : null}
                                </div>
                                <div className="text-md ps-2">
                                    {item.comments}
                                </div>
                            </div>
                        ))}
                    </SectionInner>

                    <Hr />

                    {auth?.user ? (
                        <SectionInner>
                            <form onSubmit={submitComment}>
                                <div className="text-xs text-red-600">
                                    {errors.comments}
                                </div>
                                <input
                                    className="w-full rounded"
                                    name="comments"
                                    placeholder="write your comments"
                                    value={data.comments}
                                    onChange={(e) =>
                                        setData("comments", e.target.value)
                                    }
                                />
                                <PrimaryButton disabled={processing}>
                                    submit
                                </PrimaryButton>
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
                </SectionSection>

                <RecommendedProducts products={recommendedProducts} />
            </Container>

            <TaskCounter task={task} product={product} />
        </UserLayout>
    );
}
