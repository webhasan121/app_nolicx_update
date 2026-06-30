import { Head, useForm } from "@inertiajs/react";
import { useMemo, useRef, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";
import CategorySelect from "../../../../components/CategorySelect";
import InputField from "../../../../components/InputField";
import InputFile from "../../../../components/InputFile";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import DistrictUpozilaSelect from "../../../../components/DistrictUpozilaSelect";

function youtubeEmbedUrl(url) {
    if (!url) return null;

    try {
        const normalizedUrl = String(url).match(/^https?:\/\//i)
            ? String(url)
            : `https://${String(url).replace(/^\/+/, "")}`;
        const parsed = new URL(normalizedUrl);

        if (parsed.hostname.includes("youtu.be")) {
            const id = parsed.pathname.replace("/", "");
            return id ? `https://www.youtube.com/embed/${id}` : null;
        }

        if (parsed.hostname.includes("youtube.com")) {
            const parts = parsed.pathname.split("/").filter(Boolean);
            const id =
                parsed.searchParams.get("v") ||
                (["embed", "shorts", "live"].includes(parts[0])
                    ? parts[1]
                    : parts.at(-1));
            return id ? `https://www.youtube.com/embed/${id}` : null;
        }
    } catch {
        return null;
    }

    return null;
}

export default function View({
    product,
    categories = [],
    reselDefaults,
    shop,
    totalReselProducts = 0,
    ableToAdd = false,
}) {
    const [selectedImage, setSelectedImage] = useState(
        product?.thumbnail_url ?? ""
    );
    const [showConfirm, setShowConfirm] = useState(false);
    const [showOrderModal, setShowOrderModal] = useState(false);
    const [showVideoModal, setShowVideoModal] = useState(false);
    const [isZooming, setIsZooming] = useState(false);
    const [lensPosition, setLensPosition] = useState({ x: 0, y: 0 });
    const [bgPosition, setBgPosition] = useState("0px 0px");
    const imageRef = useRef(null);
    const thumbnailScrollerRef = useRef(null);

    const form = useForm({
        resel_price: reselDefaults?.resel_price ?? "",
        resel_discount_price: reselDefaults?.resel_discount_price ?? "",
        is_resel_with_discount_price: false,
        reseller_category_id: "",
    });
    const orderForm = useForm({
        name: "",
        phone: "",
        district: "",
        upozila: "",
        location: "",
        house_no: "",
        road_no: "",
        area_condition: "",
        delevery: "",
        quantity: "",
        attr: "",
    });

    const discountPercent = useMemo(() => {
        if (!product?.offer_type || !product?.price) return 0;
        const diff = product.price - (product.discount ?? 0);
        return Math.round(((diff / product.price) * 100) || 0);
    }, [product]);

    const attrValues = useMemo(() => {
        const value = product?.attr?.value ?? "";
        if (!value) return [];
        return value.split(",").map((item) => item.trim()).filter(Boolean);
    }, [product]);
    const quantityOptions = useMemo(() => {
        if (!product?.unit || Number(product.unit) < 1) {
            return [];
        }

        return Array.from({ length: Number(product.unit) }, (_, index) => index + 1);
    }, [product]);
    const totalOrderPrice =
        (Number(orderForm.data.quantity || 0) || 0) *
        Number(product?.total_price || 0);

    const videoEmbedUrl = youtubeEmbedUrl(product?.video_url);

    const gallery = useMemo(() => {
        const items = [];

        if (videoEmbedUrl) {
            items.push({
                type: "video",
                key: `video-${product.video_url}`,
                value: product.video_url,
            });
        }

        if (product?.thumbnail_url) {
            items.push({
                type: "image",
                key: `thumb-${product.thumbnail_url}`,
                value: product.thumbnail_url,
            });
        }

        (product?.showcase ?? []).forEach((image) => {
            if (image?.url) {
                items.push({
                    type: "image",
                    key: `showcase-${image.id}`,
                    value: image.url,
                });
            }
        });

        return items.filter(
            (item, index, array) =>
                array.findIndex(
                    (candidate) =>
                        candidate.type === item.type &&
                        candidate.value === item.value,
                ) === index,
        );
    }, [product, videoEmbedUrl]);

    const handleMouseMove = (event) => {
        const image = imageRef.current;

        if (!image) return;

        const rect = image.getBoundingClientRect();
        const lensWidth = 120;
        const lensHeight = 120;
        const zoom = 4;

        let x = event.clientX - rect.left - lensWidth / 2;
        let y = event.clientY - rect.top - lensHeight / 2;

        x = Math.max(0, Math.min(x, rect.width - lensWidth));
        y = Math.max(0, Math.min(y, rect.height - lensHeight));

        setLensPosition({ x, y });
        setBgPosition(`-${x * zoom}px -${y * zoom}px`);
    };

    const scrollThumbnails = (direction) => {
        thumbnailScrollerRef.current?.scrollBy({
            left: direction * 240,
            behavior: "smooth",
        });
    };

    const confirmClone = () => {
        form.post(
            route("reseller.resel-product.clone", { product: product.id }),
            { onSuccess: () => setShowConfirm(false) }
        );
    };

    const closeOrderModal = () => {
        setShowOrderModal(false);
        orderForm.reset();
    };

    const submitOrder = (event) => {
        event.preventDefault();

        orderForm.post(
            route("reseller.resel-product.order", { product: product.id }),
            {
                onSuccess: closeOrderModal,
            },
        );
    };

    const profit = useMemo(() => {
        const base = Number(product?.total_price ?? 0);
        if (form.data.is_resel_with_discount_price) {
            return Number(form.data.resel_discount_price || 0) - base;
        }
        return Number(form.data.resel_price || 0) - base;
    }, [
        form.data.is_resel_with_discount_price,
        form.data.resel_discount_price,
        form.data.resel_price,
        product,
    ]);

    return (
        <AppLayout title="Resel Product">
            <Head title="Resel Product" />
            <style>{`
                .resel-product-zoom {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    align-items: center;
                }

                .resel-product-image-area {
                    position: relative;
                    width: 100%;
                    max-width: 420px;
                    border: 1px solid #eee;
                    background: #fff;
                    flex-shrink: 0;
                }

                .resel-product-thumbnail-strip {
                    scrollbar-width: none;
                }

                .resel-product-thumbnail-strip::-webkit-scrollbar {
                    display: none;
                }

                @media (max-width: 1024px) {
                    .resel-product-zoom {
                        display: block;
                    }

                    .resel-product-image-area {
                        width: 100%;
                    }
                }

                .resel-product-summary {
                    border: 1px solid #e7e9f3;
                    border-radius: 8px;
                    background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
                    padding: 18px;
                    box-shadow: 0 12px 28px rgba(31, 41, 55, 0.08);
                    overflow: hidden;
                }

                .resel-product-category {
                    display: inline-flex;
                    align-items: center;
                    width: auto;
                    border-radius: 999px;
                    background: #4338ca;
                    color: #fff;
                    font-size: 11px;
                    font-weight: 700;
                    letter-spacing: .08em;
                    line-height: 1;
                    padding: 8px 12px;
                    text-transform: uppercase;
                }

                .resel-product-title {
                    margin-top: 12px;
                    color: #1e1b4b;
                    font-size: 30px;
                    font-weight: 700;
                    line-height: 1.2;
                    text-transform: capitalize;
                }

                .resel-product-price {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 10px 14px;
                    margin-top: 18px;
                    color: #111827;
                }

                .resel-product-price-current {
                    font-size: 24px;
                    font-weight: 800;
                }

                .resel-product-price-label {
                    color: #4b5563;
                    font-size: 17px;
                    font-weight: 600;
                }

                .resel-product-mrp {
                    color: #6b7280;
                    font-size: 16px;
                }

                .resel-product-discount {
                    border-radius: 999px;
                    background: #fff7ed;
                    color: #ea580c;
                    font-size: 11px;
                    font-weight: 800;
                    padding: 4px 8px;
                }

                .resel-product-owner-card {
                    margin-top: 18px;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    background: #fff;
                    padding: 16px;
                }

                .resel-product-owner-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 14px 28px;
                }

                .resel-product-info-label {
                    color: #6b7280;
                    font-size: 11px;
                    font-weight: 800;
                    letter-spacing: .05em;
                    text-transform: uppercase;
                }

                .resel-product-info-value {
                    margin-top: 4px;
                    color: #1e1b4b;
                    font-size: 15px;
                    font-weight: 600;
                    overflow-wrap: anywhere;
                }

                .resel-product-visit-link {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    margin-top: 6px;
                    color: #ea580c;
                    font-size: 13px;
                    font-weight: 700;
                    text-transform: capitalize;
                }

                @media (max-width: 640px) {
                    .resel-product-summary {
                        padding: 14px;
                    }

                    .resel-product-title {
                        font-size: 24px;
                    }

                    .resel-product-image-area img {
                        height: 220px !important;
                    }

                    .resel-product-owner-grid {
                        grid-template-columns: 1fr;
                    }
                }
            `}</style>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div className="text-md">
                                    Product Review for Resel
                                </div>
                                <div className="inline-flex w-fit overflow-hidden rounded-xl border border-indigo-900 bg-indigo-900">
                                    <div
                                        className="bg-white px-2 py-1"
                                        title="Total Resell Products"
                                    >
                                        {totalReselProducts}
                                    </div>
                                    <div
                                        className="px-2 py-1 text-white"
                                        title="Max Resell Products"
                                    >
                                        {shop?.max_resell_product ?? 0}
                                    </div>
                                </div>
                            </div>
                        }
                        content={
                            <div>
                                <div>
                                    wish to resel this product, just click on
                                    the button bellow
                                </div>
                                <div className="mt-3 flex">
                                    {!ableToAdd ? (
                                        <div className="rounded-md bg-red-200 p-3 text-red-800">
                                            You have reached the maximum number
                                            of products you can upload{" "}
                                            {shop?.max_resell_product ?? 0}.
                                            Please delete some products to add
                                            new ones or{" "}
                                            <div className="text-blue-600">
                                                upgrade your plan
                                            </div>
                                            .
                                        </div>
                                    ) : (
                                        <PrimaryButton
                                            type="button"
                                            onClick={() => setShowConfirm(true)}
                                            className="w-full justify-center sm:w-auto"
                                        >
                                            <i className="pr-2 fas fa-sync"></i>{" "}
                                            resell
                                        </PrimaryButton>
                                    )}
                                </div>
                            </div>
                        }
                    />
                    <Hr />

                    <SectionInner>
                        <div className="flex flex-col gap-6 p-2 lg:flex-row lg:items-start">
                            <div className="w-full lg:max-w-[420px]">
                                <div className="resel-product-zoom">
                                    <div
                                        className="resel-product-image-area shrink-0"
                                        onMouseEnter={() =>
                                            setIsZooming(true)
                                        }
                                        onMouseLeave={() =>
                                            setIsZooming(false)
                                        }
                                        onMouseMove={handleMouseMove}
                                    >
                                        {selectedImage ? (
                                            <img
                                                ref={imageRef}
                                                className="p-2 rounded"
                                                style={{
                                                    width: "100%",
                                                    objectFit: "contain",
                                                    height: "300px",
                                                }}
                                                src={selectedImage}
                                                alt="image"
                                            />
                                        ) : null}

                                        {isZooming ? (
                                            <div
                                                style={{
                                                    position: "absolute",
                                                    width: "120px",
                                                    height: "120px",
                                                    border: "2px solid #ff6a00",
                                                    background:
                                                        "rgba(255, 255, 255, 0.35)",
                                                    left: `${lensPosition.x}px`,
                                                    top: `${lensPosition.y}px`,
                                                    pointerEvents: "none",
                                                    zIndex: 10,
                                                }}
                                            />
                                        ) : null}
                                    </div>

                                        {gallery.length > 1 ? (
                                        <div className="relative flex w-full items-center justify-center gap-2">
                                            {gallery.length > 5 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => scrollThumbnails(-1)}
                                                    className="z-10 flex h-12 w-8 shrink-0 items-center justify-center rounded border bg-white shadow-sm hover:bg-gray-50 sm:h-16"
                                                >
                                                    <i className="fas fa-angle-left"></i>
                                                </button>
                                            ) : null}

                                            <div
                                                ref={thumbnailScrollerRef}
                                                className="resel-product-thumbnail-strip flex w-full max-w-full gap-2 overflow-x-auto scroll-smooth"
                                            >
                                                {gallery.map((item) => (
                                                    <button
                                                        type="button"
                                                        className={`flex h-16 w-16 shrink-0 items-center justify-center rounded border bg-white p-1 ${
                                                            item.type === "image" && item.value === selectedImage
                                                                ? "border-orange-500"
                                                                : "border-gray-200"
                                                        }`}
                                                        key={item.key}
                                                        onClick={() => {
                                                            if (item.type === "video") {
                                                                setShowVideoModal(true);
                                                                return;
                                                            }

                                                            setSelectedImage(item.value);
                                                        }}
                                                    >
                                                        {item.type === "video" ? (
                                                            <div className="relative flex items-center justify-center w-full h-full overflow-hidden rounded bg-slate-900">
                                                                <div className="absolute inset-0 bg-black/80" />
                                                                <span className="relative z-10 flex items-center justify-center w-8 h-8 text-white rounded-full bg-black/60">
                                                                    <i className="text-xs fas fa-play"></i>
                                                                </span>
                                                            </div>
                                                        ) : (
                                                            <img
                                                                className="object-cover w-full h-full rounded"
                                                                src={item.value}
                                                                alt=""
                                                            />
                                                        )}
                                                    </button>
                                                ))}
                                            </div>

                                            {gallery.length > 5 ? (
                                                <button
                                                    type="button"
                                                    onClick={() => scrollThumbnails(1)}
                                                    className="z-10 flex h-12 w-8 shrink-0 items-center justify-center rounded border bg-white shadow-sm hover:bg-gray-50 sm:h-16"
                                                >
                                                    <i className="fas fa-angle-right"></i>
                                                </button>
                                            ) : null}
                                        </div>
                                    ) : null}
                                </div>
                            </div>

                            <div className="relative w-full min-w-0 flex-1 py-1 lg:py-0">
                                <div
                                    style={{
                                        position: "absolute",
                                        top: "0",
                                        right: "0",
                                        width: "100%",
                                        height: "420px",
                                        border: "1px solid #eee",
                                        backgroundRepeat: "no-repeat",
                                        backgroundColor: "#fff",
                                        display:
                                            isZooming &&
                                            typeof window !== "undefined" &&
                                            window.innerWidth > 1024
                                                ? "block"
                                                : "none",
                                        zIndex: 30,
                                        boxShadow:
                                            "0 8px 24px rgba(0,0,0,.15)",
                                        backgroundImage: selectedImage
                                            ? `url('${selectedImage}')`
                                            : "none",
                                        backgroundSize: "1680px 1680px",
                                        backgroundPosition: bgPosition,
                                    }}
                                />
                                <div className="resel-product-summary">
                                    <div
                                        className="text-gray-400 rounded bold"
                                        style={{ fontSize: "12px" }}
                                    >
                                        <NavLink
                                            href={route(
                                                "reseller.resel-product.index",
                                                {
                                                    cat: product?.category?.id,
                                                }
                                            )}
                                            className="resel-product-category hover:text-white"
                                        >
                                            {product?.category?.name ??
                                                "Undefined"}
                                        </NavLink>
                                    </div>
                                    <div className="resel-product-title">
                                        {product?.title ?? ""}
                                    </div>

                                    <div className="py-2">
                                        {product?.attr?.name ? (
                                            <>
                                                <hr />
                                                <h4 className="">
                                                    {product.attr.name}
                                                </h4>
                                                <div
                                                    className="my-1 flex items-center justify-start"
                                                    style={{
                                                        flexWrap: "wrap",
                                                        gap: "10px",
                                                    }}
                                                >
                                                    {attrValues.map((attr) => (
                                                        <div
                                                            key={attr}
                                                            className="mr-2 flex items-center justify-center rounded border px-2"
                                                            style={{
                                                                minWidth: "45px",
                                                                height: "35px",
                                                            }}
                                                        >
                                                            {attr}
                                                        </div>
                                                    ))}
                                                </div>
                                                <hr />
                                            </>
                                        ) : null}
                                    </div>

                                    <div className="resel-product-price">
                                        {product?.offer_type ? (
                                            <>
                                                <div>
                                                    <span className="resel-product-price-label">
                                                        Price:
                                                    </span>{" "}
                                                    <strong className="resel-product-price-current">
                                                        {product.total_price} TK
                                                    </strong>
                                                </div>
                                                <del className="resel-product-mrp">
                                                    MRP: {product.price} TK
                                                </del>
                                                <span className="resel-product-discount">
                                                    {discountPercent}% OFF
                                                </span>
                                            </>
                                        ) : (
                                            <div>
                                                <span className="resel-product-price-label">
                                                    Price:
                                                </span>{" "}
                                                <strong className="resel-product-price-current">
                                                    {product?.total_price} TK
                                                </strong>
                                            </div>
                                        )}
                                    </div>

                                    <div className="resel-product-owner-card">
                                        <div className="resel-product-owner-grid">
                                            <div>
                                                <div>
                                                    <div className="resel-product-info-label">
                                                        Vendor
                                                    </div>
                                                    <NavLink className="resel-product-info-value">
                                                        {product?.owner?.name ??
                                                            "n/a"}
                                                    </NavLink>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <div className="resel-product-info-label">
                                                        Phone
                                                    </div>
                                                    <div className="resel-product-info-value">
                                                        {product?.owner?.phone ??
                                                            "n/a"}
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <div className="resel-product-info-label">
                                                        Shop / Brand
                                                    </div>
                                                    <div className="resel-product-info-value">
                                                        {product?.owner?.shop
                                                            ?.shop_name_en ?? "n/a"}{" "}
                                                        <span className="text-xs font-normal text-gray-500">
                                                            (
                                                            {product?.owner?.shop
                                                                ?.shop_name_bn ??
                                                                "n/a"}
                                                        )
                                                        </span>
                                                        <br />
                                                        <NavLink
                                                            className="resel-product-visit-link hover:text-orange-700"
                                                            href={route("shops", {
                                                                slug: product?.owner
                                                                    ?.shop?.slug,
                                                                id: product?.owner
                                                                    ?.shop?.id,
                                                            })}
                                                        >
                                                            visit shops{" "}
                                                            <i className="fas fa-angle-right"></i>
                                                        </NavLink>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <div className="resel-product-info-label">
                                                        Email
                                                    </div>
                                                    <div className="resel-product-info-value">
                                                        {product?.owner?.email ??
                                                            "n/a"}
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <div className="resel-product-info-label">
                                                        Address
                                                    </div>
                                                    <div className="resel-product-info-value text-sm">
                                                        {product?.owner?.address ??
                                                            "n/a"}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-4">
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => setShowOrderModal(true)}
                                        className="flex w-full justify-between px-6 py-3 sm:min-w-44 sm:w-auto"
                                    >
                                        Purchase
                                        <i className="pl-4 fas fa-angle-right"></i>
                                    </PrimaryButton>
                                </div>

                            </div>
                        </div>
                    </SectionInner>
                </Section>

                <Section>
                    <SectionInner>
                        <div className="font-bold">Description</div>
                        <div
                            className="overflow-x-auto break-words [&_img]:h-auto [&_img]:max-w-full [&_table]:w-full"
                            dangerouslySetInnerHTML={{
                                __html: product?.description ?? "",
                            }}
                        />
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showConfirm} onClose={() => setShowConfirm(false)}>
                <div className="p-2 px-4 sm:px-5">
                    <div className="py-2 font-bold">Resel Product</div>
                    <Hr />
                    <div className="text-sm">
                        Your are going to add this product to your product list
                        to resell this with a veiw to earn more profit with your
                        custom price. Your may able to update product price
                        after product successfully cloed to your product list.
                        By clicking <strong>CONFIRM</strong> button, bellow task
                        goig to be done ...
                        <Hr />
                        <ul list-item="number">
                            <li>
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
                                Product going to be add to your product list.
                            </li>
                            <li>
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
                                Sytem take a track for your reseling.
                            </li>
                            <li>
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
                                Product owner get a message from you that you
                                are reselling this products.
                            </li>
                        </ul>
                    </div>
                    <Hr />
                    <div>
                    <div className="mb-2 bg-gray-100 p-3">
                            <div className="mb-1">
                                {product?.thumbnail_url ? (
                                    <img
                                        src={product.thumbnail_url}
                                        className="w-12 h-12 mb-2 rounded-md"
                                        alt=""
                                    />
                                ) : null}

                                <p className="text-lg">{product?.title}</p>

                                {product?.offer_type ? (
                                    <div className="flex flex-col gap-1 md:flex-row md:items-baseline">
                                        <div className="font-normal font-bold text-md">
                                            Price :{" "}
                                            <strong>
                                                {product?.total_price} TK
                                            </strong>
                                        </div>
                                        <div className="flex items-baseline justify-start">
                                            <del
                                                className="px-1"
                                                style={{ fontSize: "14px" }}
                                            >
                                                MRP: {product?.price} TK
                                            </del>
                                            <div className="text-xs">
                                                {discountPercent}% OFF
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div
                                        style={{
                                            fontWeight: "bold",
                                            fontSize: "18px",
                                            color: "var(--brand-primary)",
                                            marginRight: "12px",
                                        }}
                                    >
                                        Price : {product?.total_price} TK
                                    </div>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="text-sm font-bold">
                                Resel Price
                            </label>
                            <input
                                type="number"
                                min={product?.price ?? 0}
                                className="w-full rounded"
                                value={form.data.resel_price}
                                onChange={(e) =>
                                    form.setData(
                                        "resel_price",
                                        e.target.value
                                    )
                                }
                            />
                            {form.errors.resel_price ? (
                                <p className="text-red-400">
                                    {form.errors.resel_price}
                                </p>
                            ) : null}
                        </div>
                        <div className="p-2 mt-2 border rounded shadow">
                            <div className="flex justify-between mb-2">
                                <label className="text-sm font-bold">
                                    Resel Discount Price
                                </label>
                                <input
                                    type="checkbox"
                                    value="true"
                                    checked={
                                        form.data.is_resel_with_discount_price
                                    }
                                    onChange={(e) =>
                                        form.setData(
                                            "is_resel_with_discount_price",
                                            e.target.checked
                                        )
                                    }
                                    style={{ width: "20px", height: "20px" }}
                                />
                            </div>
                            {form.data.is_resel_with_discount_price ? (
                                <div>
                                    <input
                                        placeholder="Discount Price"
                                        min={product?.price ?? 0}
                                        type="number"
                                        className="w-full rounded"
                                        value={form.data.resel_discount_price}
                                        onChange={(e) =>
                                            form.setData(
                                                "resel_discount_price",
                                                e.target.value
                                            )
                                        }
                                    />
                                    {form.errors.resel_discount_price ? (
                                        <p className="text-red-400">
                                            {form.errors.resel_discount_price}
                                        </p>
                                    ) : null}
                                </div>
                            ) : null}
                        </div>
                        <div className="mt-2">
                            Profit :
                            {form.data.is_resel_with_discount_price ? (
                                <>
                                    <span className="px-2 text-red-500">
                                        (with discount)
                                    </span>
                                    <span className="text-red-500">
                                        {profit}
                                    </span>
                                </>
                            ) : (
                                <>
                                    <span className="px-2 text-red-500">
                                        (without discount)
                                    </span>
                                    <span className="text-red-500">
                                        {profit}
                                    </span>
                                </>
                            )}
                        </div>
                        <Hr />
                        <div>
                            <label className="text-sm font-bold">
                                Reseller Category
                            </label>
                            <CategorySelect
                                categories={categories}
                                className="mt-1"
                                inputClassName="border rounded"
                                value={form.data.reseller_category_id}
                                onChange={(categoryId) =>
                                    form.setData(
                                        "reseller_category_id",
                                        categoryId
                                    )
                                }
                                placeholder="Select Category"
                                noneLabel="Select Category"
                            />
                            {form.errors.reseller_category_id ? (
                                <p className="text-red-900">
                                    {form.errors.reseller_category_id}
                                </p>
                            ) : null}
                        </div>
                    </div>
                    <Hr />
                    For procced, click to confirm button. After successfully
                    cloned, You can update resel product from your product list.
                    <div className="flex items-start justify-end p-2">
                        <PrimaryButton
                            type="button"
                            onClick={confirmClone}
                            className="w-full justify-center sm:w-auto"
                        >
                            <i className="pr-2 fas fa-sync"></i> Confirm
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>

            <Modal
                show={showOrderModal}
                onClose={closeOrderModal}
                maxWidth="md"
            >
                <div>
                    <div className="bold flex flex-col gap-2 border-b p-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>Purchase</div>
                        <div className="text-lg bold">
                            {product?.total_price} TK
                        </div>
                    </div>
                    <div className="mb-3 flex flex-col gap-3 bg-gray-100 p-4 sm:flex-row sm:items-start sm:justify-start sm:p-5">
                        <div className="flex shrink-0">
                            {product?.thumbnail_url ? (
                                <img
                                    src={product.thumbnail_url}
                                    className="h-12 w-12 rounded shadow sm:mr-3"
                                    alt=""
                                />
                            ) : null}
                        </div>
                        <div className="min-w-0">
                            <div className="text-lg bold">
                                {product?.name ?? product?.title ?? "N/A"}
                            </div>
                            <div className="text-sm">
                                {product?.offer_type ? (
                                    <div className="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:gap-2">
                                        <div className="bold">
                                            Price : {product?.total_price} TK
                                        </div>
                                        <div className="text-xs">
                                            <del>
                                                MRP: {product?.price ?? "0"} TK
                                            </del>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="bold">
                                        Price : {product?.price ?? "0"} TK
                                    </div>
                                )}
                                <div className="text-xs">
                                    Available Stock: {product?.unit ?? "0"}
                                </div>
                            </div>
                        </div>
                    </div>

                    <form onSubmit={submitOrder} className="p-4 sm:p-5">
                        <InputField
                            className="md:flex"
                            labelWidth="140px"
                            label="Name"
                            name="name"
                            inputClass="w-full"
                            error={orderForm.errors.name}
                            value={orderForm.data.name}
                            onChange={(e) =>
                                orderForm.setData("name", e.target.value)
                            }
                        />
                        <InputField
                            className="md:flex"
                            labelWidth="140px"
                            label="Phone"
                            name="phone"
                            inputClass="w-full"
                            error={orderForm.errors.phone}
                            value={orderForm.data.phone}
                            onChange={(e) =>
                                orderForm.setData("phone", e.target.value)
                            }
                        />
                        <DistrictUpozilaSelect
                            district={orderForm.data.district}
                            upozila={orderForm.data.upozila}
                            errors={orderForm.errors}
                            labelWidth="140px"
                            onDistrictChange={(value) =>
                                orderForm.setData("district", value)
                            }
                            onUpozilaChange={(value) =>
                                orderForm.setData("upozila", value)
                            }
                        />
                        <InputFile
                            labelWidth="140px"
                            label="Full Address"
                            name="location"
                            error="location"
                            errors={orderForm.errors}
                        >
                            <textarea
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="3"
                                placeholder="Full Address"
                                value={orderForm.data.location}
                                onChange={(e) =>
                                    orderForm.setData("location", e.target.value)
                                }
                            />
                        </InputFile>
                        <InputField
                            className="md:flex"
                            labelWidth="140px"
                            label="Road No"
                            name="road_no"
                            inputClass="w-full"
                            error={orderForm.errors.road_no}
                            value={orderForm.data.road_no}
                            onChange={(e) =>
                                orderForm.setData("road_no", e.target.value)
                            }
                        />
                        <InputField
                            className="md:flex"
                            labelWidth="140px"
                            label="House No"
                            name="house_no"
                            inputClass="w-full"
                            error={orderForm.errors.house_no}
                            value={orderForm.data.house_no}
                            onChange={(e) =>
                                orderForm.setData("house_no", e.target.value)
                            }
                        />

                        <InputFile
                            labelWidth="140px"
                            label="Quantity"
                            name="quantity"
                            error="quantity"
                            errors={orderForm.errors}
                        >
                            <select
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value={orderForm.data.quantity}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "quantity",
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">Select Quantity</option>
                                {quantityOptions.map((qty) => (
                                    <option key={qty} value={qty}>
                                        {qty}
                                    </option>
                                ))}
                            </select>
                        </InputFile>

                        <div className="my-3 rounded-md bg-indigo-50 p-3 text-sm shadow-sm">
                            <div className="text-xs">
                                {Number(product?.unit) < 1
                                    ? "Stock Out"
                                    : `You can order maximum ${product?.unit} item`}
                            </div>
                            <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>Total</div>
                                <div>
                                    {orderForm.data.quantity || 0} *{" "}
                                    {product?.total_price} = {totalOrderPrice}
                                </div>
                            </div>
                        </div>

                        <InputFile
                            labelWidth="140px"
                            label="Size/Attribute"
                            name="attr"
                            error="attr"
                            errors={orderForm.errors}
                        >
                            <select
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value={orderForm.data.attr}
                                onChange={(e) =>
                                    orderForm.setData("attr", e.target.value)
                                }
                            >
                                <option value="">Select Size/Attribute</option>
                                {attrValues.length > 0 ? (
                                    attrValues.map((attr) => (
                                        <option key={attr} value={attr}>
                                            {attr}
                                        </option>
                                    ))
                                ) : (
                                    <option value="N/A">N/A</option>
                                )}
                            </select>
                        </InputFile>

                        <Hr />

                        <InputFile
                            labelWidth="140px"
                            label="Area"
                            name="area_condition"
                            error="area_condition"
                            errors={orderForm.errors}
                        >
                            <select
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value={orderForm.data.area_condition}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "area_condition",
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">Select Area</option>
                                <option value="Dhaka">Inside Dhaka</option>
                                <option value="Other">Out side of Dhaka</option>
                            </select>
                        </InputFile>

                        <InputFile
                            labelWidth="140px"
                            label="Shipping Type"
                            name="delevery"
                            error="delevery"
                            errors={orderForm.errors}
                        >
                            <select
                                className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value={orderForm.data.delevery}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "delevery",
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">Shipping Type</option>
                                <option value="Courier">Courier</option>
                                <option value="Home">Home Delivery</option>
                                <option value="Hand">Hand-To-Hand</option>
                            </select>
                        </InputFile>

                        <PrimaryButton
                            type="submit"
                            disabled={orderForm.processing}
                            className="w-full justify-center sm:w-auto"
                        >
                            Order
                        </PrimaryButton>
                    </form>
                </div>
            </Modal>

            {showVideoModal && videoEmbedUrl ? (
                <div
                    className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/70"
                    onClick={() => setShowVideoModal(false)}
                >
                    <div
                        className="relative w-full max-w-4xl p-3 bg-white rounded-lg shadow-2xl"
                        onClick={(event) => event.stopPropagation()}
                    >
                        <button
                            type="button"
                            onClick={() => setShowVideoModal(false)}
                            className="absolute flex items-center justify-center w-10 h-10 text-white rounded-full top-3 right-3 bg-black/70 hover:bg-black"
                        >
                            <i className="fas fa-times"></i>
                        </button>

                        <iframe
                            key={videoEmbedUrl}
                            src={`${videoEmbedUrl}?autoplay=1`}
                            title={product.title}
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowFullScreen
                            className="w-full rounded-lg aspect-video bg-black"
                        />
                    </div>
                </div>
            ) : null}
        </AppLayout>
    );
}
