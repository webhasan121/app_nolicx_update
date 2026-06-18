import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import UserDash from "../../../components/user/dash/UserDash";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import DangerButton from "../../../components/DangerButton";
import SecondaryButton from "../../../components/SecondaryButton";
import Modal from "../../../components/Modal";
import InputError from "../../../components/InputError";
import Hr from "../../../components/Hr";
import CartSummaryPanel from "../../../components/user/CartSummaryPanel";
import ProductName from "../../../components/ProductName";
import useTranslation from "../../../hooks/useTranslation";

const activeStatuses = {
    Placed: ["Pending", "Accept", "Picked", "Delivery", "Delivered", "Confirm"],
    Accept: ["Accept", "Picked", "Delivery", "Delivered", "Confirm"],
    Collecting: ["Picked", "Delivery", "Delivered", "Confirm"],
    Delivery: ["Delivery", "Delivered", "Confirm"],
    Delivered: ["Delivered", "Confirm"],
    Confirm: ["Confirm"],
};

function StatusBox({ label, orderStatus, statuses, title }) {
    const active = statuses.includes(orderStatus);

    return (
        <div
            className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                active ? "bg-indigo-900 text-white" : "bg-gray-100 text-gray-600"
            }`}
            title={title}
        >
            {label}
            <br />
            <div className={active ? "block" : "hidden"}>
                <i className="fas fa-check-circle"></i>
            </div>
        </div>
    );
}

function TimelineItem({ title, description }) {
    return (
        <div className="relative flex items-center px-2 py-2 border-l">
            <i
                className="absolute w-12 h-12 fas fa-check-circle"
                style={{ left: "-8px", top: "12px" }}
            ></i>
            <div className="px-4">
                <p>{title}</p>
                {description ? <p className="text-xs">{description}</p> : null}
            </div>
        </div>
    );
}

function buildOrderTimeline(order, t) {
    if (!order) {
        return [];
    }

    const placed = {
        title: t("Placed the order"),
        description: order.created_at,
    };
    const accepted = {
        title: t("Order has been accepted by seller."),
    };
    const packed = {
        title: t("Order Packed."),
        description: t("Order product has been packed and ready for shipment."),
    };
    const sent = {
        title: t("Order Send"),
        description:
            order.delevery === "cash"
                ? `${t("Order has been send to")} ${order.location ?? "N/A"}`
                : `${t("Order has beed send to")} ${order.location ?? "N/A"}.`,
    };
    const assigned = {
        title: order.status === "Delivery" ? t("Order Assignedd to Rider") : t("Order Assigned"),
        description:
            order.delevery === "cash" && order.assigned_rider?.name
                ? `${t("Assigned to rider")} ${order.assigned_rider.name}`
                : "",
    };
    const delivered = {
        title: t("Delivered"),
        description: t("Order has been marked as delivered to you by rider at."),
    };
    const finished = {
        title: t("Success and Finished"),
    };

    switch (order.status) {
        case "Pending":
            return [placed];
        case "Accept":
            return [accepted, placed];
        case "Picked":
            return [packed, accepted, placed];
        case "Delivery":
            return order.assigned_rider
                ? [assigned, sent, packed, accepted, placed]
                : [sent, packed, accepted, placed];
        case "Delivered":
            return order.assigned_rider
                ? [delivered, assigned, sent, packed, accepted, placed]
                : [delivered, sent, packed, accepted, placed];
        case "Confirm":
            return [finished, assigned, sent, packed, accepted, placed];
        default:
            return [];
    }
}

export default function OrderDetails() {
    const { order } = usePage().props;
    const { t } = useTranslation();
    const [showRatingModal, setShowRatingModal] = useState(false);
    const [hoveredRatings, setHoveredRatings] = useState({});
    const [reviewImagePreviews, setReviewImagePreviews] = useState({});
    const reviewImagePreviewsRef = useRef({});
    const orderTotal = Number(order?.total ?? 0);
    const shippingTotal = Number(order?.shipping ?? 0);
    const payableTotal = orderTotal + shippingTotal;
    const orderTimeline = buildOrderTimeline(order, t);
    const summaryItems = order.cart_orders.map((item) => ({
        id: item.id,
        href: route("products.details", {
            id: item.product?.id ?? "",
            slug: item.product?.slug ?? "",
        }),
        image: item.product?.thumbnail
            ? `/storage/${item.product.thumbnail}`
            : "",
        name: item.product?.name,
        shop: item.product?.shop_name,
        quantity: item.quantity,
        attribute: item.size || "-",
        total: item.total,
        priceText: `${item.price} x ${item.quantity} = ${item.total} TK`,
    }));
    const { data, setData, post, processing, errors } = useForm({
        reviews: order.cart_orders.map((item) => ({
            cart_order_id: item.id,
            product_id: item.product?.id,
            rating: item.review?.rating ?? 5,
            comments: item.review?.comments ?? "",
            images: [],
        })),
    });

    useEffect(() => {
        return () => {
            Object.values(reviewImagePreviewsRef.current)
                .flat()
                .forEach((preview) => URL.revokeObjectURL(preview.url));
        };
    }, []);

    const markAsReceived = () => {
        post(route("user.orders.received", { id: order.id }), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => setShowRatingModal(false),
        });
    };

    const updateReview = (index, field, value) => {
        setData(
            "reviews",
            data.reviews.map((review, reviewIndex) =>
                reviewIndex === index ? { ...review, [field]: value } : review,
            ),
        );
    };

    const updateReviewImages = (index, files) => {
        const selectedFiles = Array.from(files || []).slice(0, 3);

        (reviewImagePreviews[index] ?? []).forEach((preview) =>
            URL.revokeObjectURL(preview.url),
        );

        updateReview(index, "images", selectedFiles);
        setReviewImagePreviews((items) => {
            const nextItems = {
                ...items,
                [index]: selectedFiles.map((file) => ({
                name: file.name,
                url: URL.createObjectURL(file),
                })),
            };

            reviewImagePreviewsRef.current = nextItems;
            return nextItems;
        });
    };

    const clearReviewImages = (index) => {
        (reviewImagePreviews[index] ?? []).forEach((preview) =>
            URL.revokeObjectURL(preview.url),
        );

        updateReview(index, "images", []);
        setReviewImagePreviews((items) => {
            const nextItems = { ...items };
            delete nextItems[index];
            reviewImagePreviewsRef.current = nextItems;
            return nextItems;
        });
    };

    const selectedRating = (index) => Number(data.reviews[index]?.rating ?? 0);

    const displayedRating = (index) =>
        Number(hoveredRatings[index] ?? selectedRating(index));

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div>
                                {t("Order Details")}
                                <br />
                                <div className="text-xs">
                                    {order.created_at} {t("at")} {order.created_time}
                                </div>
                            </div>
                        }
                        content={
                            <div>
                                <div>{t("Order Id")} : {order.id}</div>
                                <div className="items-center justify-between w-full space-y-2 overflow-hidden overflow-x-auto md:flex">
                                    <div>
                                        <div className="flex gap-2 mb-2">
                                            <StatusBox
                                                label={t("Placed")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Placed}
                                                title={t("Buyer placed the order. Order in Pending")}
                                            />
                                            <StatusBox
                                                label={t("Accept")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Accept}
                                                title={t("Accept the order for process")}
                                            />
                                            <StatusBox
                                                label={order.status === "Picked" ? t("Collected") : t("Collecting")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Collecting}
                                                title={t("Find and collect the product")}
                                            />
                                            <StatusBox
                                                label={t("Delivery")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Delivery}
                                                title={t("product shipped to rider or courier.")}
                                            />
                                            <StatusBox
                                                label={t("Delivered")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Delivered}
                                                title={t("product delivered to the buyer.and buyer successfully received the order")}
                                            />
                                            <StatusBox
                                                label={t("Confirm")}
                                                orderStatus={order.status}
                                                statuses={activeStatuses.Confirm}
                                                title={t("Confirmed")}
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <div className="flex gap-2 mb-2">
                                            {order.status === "Delivered" &&
                                                (order.received_at ? (
                                                    <div className="flex items-center gap-2 p-2 px-3 text-center text-white bg-indigo-900 rounded-md cursor-pointer">
                                                        <i className="fas fa-check-circle"></i>
                                                        <span>{t("Already Received")}</span>
                                                    </div>
                                                ) : (
                                                    <PrimaryButton onClick={() => setShowRatingModal(true)}>
                                                        {t("Mark as Received")}
                                                    </PrimaryButton>
                                                ))}
                                            <div
                                                className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                                    order.status === "Hold"
                                                        ? "bg-indigo-900 text-white"
                                                        : "bg-gray-100 text-gray-600"
                                                }`}
                                            >
                                                {t("Hold")}
                                                <br />
                                                <div className={order.status === "Hold" ? "block" : "hidden"}>
                                                    <i className="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <div
                                                className={`p-2 px-3 rounded-md cursor-pointer text-center ${
                                                    order.status === "Reject"
                                                        ? "bg-indigo-900 text-white"
                                                        : "bg-gray-100 text-gray-600"
                                                }`}
                                            >
                                                {t("Reject")}
                                                <br />
                                                <div className={order.status === "Reject" ? "block" : "hidden"}>
                                                    <i className="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                        {order.status === "Rejecte" && (
                                            <DangerButton>{t("Order Cancelled")}</DangerButton>
                                        )}
                                    </div>
                                </div>
                            </div>
                        }
                    />
                </SectionSection>

                <CartSummaryPanel
                    title={t("Order Items")}
                    subtitle={`${t("Order Id")} : ${order.id}`}
                    items={summaryItems}
                    totals={[
                        { label: t("Total"), value: `${orderTotal} TK` },
                        { label: t("Shipping"), value: `${shippingTotal} TK` },
                        {
                            label: t("Payable"),
                            value: `${payableTotal} TK`,
                            emphasis: true,
                        },
                    ]}
                />

                <div className="max-w-md">
                    <SectionSection>
                        <div className="flex items-center justify-between">
                            <div>{t("Shipping")}</div>
                            <div>
                                <div className="px-2 py-1 text-white bg-indigo-900 rounded-lg">
                                    {shippingTotal} TK
                                </div>
                            </div>
                        </div>

                        <div className="pt-2">
                            <div className="flex items-center text-xs sapce-x-2">
                                {order.delevery} {t("Delivery")}{" "}
                                {order.area_condition === "Dhaka"
                                    ? t("in Dhaka")
                                    : t("Outside of Dhaka")}
                            </div>
                        </div>

                        <div className="mb-10 text-sm">
                            <b>{order.location ?? "N/A"}</b>
                            <br />
                            {t("Phone")} : {order.number ?? "N/A"}
                        </div>

                        <div className="mb-6">
                            <h3 className="mb-2">{t("Assign To")}</h3>
                            <Hr />
                            <div>
                                {order.assigned_rider ? (
                                    <div>
                                        <div className="text-lg font-bold text-bold">
                                            {order.assigned_rider.name}
                                        </div>
                                        <p>{order.assigned_rider.phone}</p>
                                    </div>
                                ) : (
                                    <div>N/A</div>
                                )}
                            </div>
                        </div>

                        <div>
                            {orderTimeline.map((item, index) => (
                                <TimelineItem
                                    key={`${item.title}-${index}`}
                                    title={item.title}
                                    description={item.description}
                                />
                            ))}
                        </div>
                    </SectionSection>
                </div>
            </Container>

            <Modal show={showRatingModal} onClose={() => setShowRatingModal(false)} maxWidth="2xl">
                <div className="p-6">
                    <div className="mb-4">
                        <h2 className="text-lg font-bold">{t("Rate Your Order")}</h2>
                        <p className="text-sm text-gray-500">
                            {t("Submit your product rating before marking this order as received.")}
                        </p>
                    </div>

                    <div className="space-y-4">
                        {order.cart_orders.map((item, index) => (
                            <div key={item.id} className="space-y-3">
                                <div className="flex items-center gap-3">
                                    {item.product?.thumbnail ? (
                                        <img
                                            width="48"
                                            height="48"
                                            className="object-cover border rounded-md bg-gray-50"
                                            src={`/storage/${item.product.thumbnail}`}
                                            alt=""
                                        />
                                    ) : (
                                        <div className="flex items-center justify-center w-12 h-12 text-gray-400 border rounded-md bg-gray-50">
                                            <i className="fas fa-image"></i>
                                        </div>
                                    )}
                                    <div className="font-semibold leading-6">
                                        <ProductName value={item.product?.name} />
                                    </div>
                                </div>

                                <div className="p-3 bg-white border border-orange-200 rounded-lg shadow-sm focus-within:border-orange-400">
                                    <textarea
                                        className="w-full min-h-[150px] resize-y border-0 px-1 py-2 text-sm shadow-none focus:border-0 focus:ring-0"
                                        required
                                        placeholder={t("Write Your Comments")}
                                        value={data.reviews[index]?.comments ?? ""}
                                        onChange={(event) =>
                                            updateReview(index, "comments", event.target.value)
                                        }
                                    />

                                    <div className="flex flex-wrap items-center justify-between gap-3 pt-3 mt-3 border-t border-slate-100">
                                        <div>
                                            <div className="flex items-center gap-1">
                                            {[1, 2, 3, 4, 5].map((star) => (
                                                <button
                                                    key={star}
                                                    type="button"
                                                    className="inline-flex items-center justify-center w-8 h-8 text-2xl transition hover:scale-110 focus:outline-none"
                                                    onClick={() => updateReview(index, "rating", star)}
                                                    onMouseEnter={() =>
                                                        setHoveredRatings((ratings) => ({
                                                            ...ratings,
                                                            [index]: star,
                                                        }))
                                                    }
                                                    onMouseLeave={() =>
                                                        setHoveredRatings((ratings) => {
                                                            const nextRatings = { ...ratings };
                                                            delete nextRatings[index];
                                                            return nextRatings;
                                                        })
                                                    }
                                                    aria-label={`${t("Rate")} ${star} ${t("out of 5")}`}
                                                    title={`${star} ${t("star")}`}
                                                >
                                                    <i
                                                        className="fas fa-star"
                                                        style={{
                                                            color: displayedRating(index) >= star ? "#fbbf24" : "#d1d5db",
                                                        }}
                                                    ></i>
                                                </button>
                                            ))}
                                            <span className="pl-2 text-xs text-slate-500">
                                                {selectedRating(index) ? `${selectedRating(index)}/5` : t("Add rating")}
                                            </span>
                                            </div>
                                            <InputError messages={errors[`reviews.${index}.rating`]} />
                                        </div>

                                        <label className="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-md cursor-pointer border-slate-200 text-slate-600 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600">
                                            <i className="fas fa-images"></i>
                                            {t("Upload Images")}
                                            <input
                                                type="file"
                                                accept="image/*"
                                                multiple
                                                className="hidden"
                                                onChange={(event) =>
                                                    updateReviewImages(index, event.target.files)
                                                }
                                            />
                                        </label>
                                    </div>

                                    <InputError messages={errors[`reviews.${index}.comments`]} />
                                    <InputError messages={errors[`reviews.${index}.images`]} />
                                    <InputError messages={errors[`reviews.${index}.images.0`]} />
                                    <InputError messages={errors[`reviews.${index}.images.1`]} />
                                    <InputError messages={errors[`reviews.${index}.images.2`]} />

                                    {reviewImagePreviews[index]?.length ? (
                                        <div className="flex flex-wrap gap-2 mt-3">
                                            {reviewImagePreviews[index].map((preview) => (
                                                <img
                                                    key={preview.url}
                                                    src={preview.url}
                                                    alt={preview.name}
                                                    className="object-cover w-16 h-16 border rounded-md border-slate-200"
                                                />
                                            ))}
                                            <button
                                                type="button"
                                                className="h-16 px-3 text-xs border rounded-md border-slate-200 text-slate-500 hover:bg-slate-50"
                                                onClick={() => clearReviewImages(index)}
                                            >
                                                {t("Clear")}
                                            </button>
                                        </div>
                                    ) : null}
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="flex justify-end gap-2 mt-6">
                        <SecondaryButton onClick={() => setShowRatingModal(false)}>
                            {t("Cancel")}
                        </SecondaryButton>
                        <PrimaryButton onClick={markAsReceived} disabled={processing}>
                            {t("Submit Rating & Mark Received")}
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </UserDash>
    );
}
