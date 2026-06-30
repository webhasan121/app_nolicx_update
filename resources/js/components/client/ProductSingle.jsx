import { Link, router, usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import axios from "axios";
import SecondaryButton from "../SecondaryButton";
import Hr from "../Hr";
import NavLink from "../NavLink";
import ProductsLoop from "./ProductsLoop";
import ProductAttributesSelector from "./ProductAttributesSelector";
import Swal from "sweetalert2";

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
                (["embed", "shorts", "live"].includes(parts[0]) ? parts[1] : parts.at(-1));
            return id ? `https://www.youtube.com/embed/${id}` : null;
        }
    } catch {
        return null;
    }

    return null;
}

function RatingStars({ rating = 0 }) {
    const roundedRating = Math.round(Number(rating || 0));

    return (
        <>
            {[1, 2, 3, 4, 5].map((star) => (
                <i
                    key={star}
                    className="fas fa-star"
                    style={{
                        color:
                            roundedRating >= star
                                ? "var(--brand-primary)"
                                : "#737272",
                    }}
                ></i>
            ))}
        </>
    );
}

export default function ProductSingle({
    product,
    relatedProduct = [],
    onBuyNowClick = null,
    initialSelectedAttrs = {},
    onSelectedAttrsChange = null,
    savedForLater = null,
    onSaveForLaterChange = null,
}) {
    const { auth } = usePage().props;
    const [copied, setCopied] = useState(false);
    const [previewImage, setPreviewImage] = useState(product?.thumbnail);
    const [showVideoModal, setShowVideoModal] = useState(false);
    const [selectedAttrs, setSelectedAttrs] = useState(initialSelectedAttrs ?? {});
    const [isSavedForLater, setIsSavedForLater] = useState(
        Boolean(savedForLater ?? product?.is_saved_for_later),
    );
    const [savingForLater, setSavingForLater] = useState(false);
    const [isZooming, setIsZooming] = useState(false);
    const [lensPosition, setLensPosition] = useState({ x: 0, y: 0 });
    const [bgPosition, setBgPosition] = useState("0px 0px");
    const imageRef = useRef(null);
    const thumbnailScrollerRef = useRef(null);

    useEffect(() => {
        setSelectedAttrs(initialSelectedAttrs ?? {});
    }, [JSON.stringify(initialSelectedAttrs ?? {})]);

    useEffect(() => {
        onSelectedAttrsChange?.(selectedAttrs);
    }, [JSON.stringify(selectedAttrs)]);

    useEffect(() => {
        setIsSavedForLater(Boolean(savedForLater ?? product?.is_saved_for_later));
    }, [savedForLater, product?.is_saved_for_later]);

    if (!product) return null;

    const galleryImages = [...new Set([
        product.thumbnail,
        ...(product?.showcase?.map((image) => image?.image) ?? []),
    ].filter(Boolean))];

    const videoEmbedUrl = youtubeEmbedUrl(product?.video_url);
    const videoItem = videoEmbedUrl ? { type: "video", value: product.video_url } : null;
    const gallery = [
        ...(videoItem ? [videoItem] : []),
        ...galleryImages.map((image) => ({ type: "image", value: image })),
    ];

    const discountPercentage =
        product.offer_type && product.price
            ? Math.round(
                  ((Number(product.price) - Number(product.discount)) /
                      Number(product.price)) *
                      100,
              )
            : null;

    const rawPhone = product?.owner?.shop?.phone;
    const whatsappPhone = rawPhone
        ? `880${String(rawPhone).replace(/\D/g, "").replace(/^0+/, "")}`
        : null;
    const productUrl =
        typeof window !== "undefined"
            ? `${window.location.origin}${route("products.details", {
                  id: product.id,
                  slug: product.slug,
              })}`
            : route("products.details", {
                  id: product.id,
                  slug: product.slug,
              });
    const selectedAttrQuery = Object.fromEntries(
        Object.entries(selectedAttrs).filter(([, value]) => value)
    );
    const buyNowHref = (() => {
        const href = route("product.makeOrder", { id: product.id, slug: product.slug });

        if (!Object.keys(selectedAttrQuery).length) {
            return href;
        }

        return `${href}?${new URLSearchParams({
            selected_attrs: JSON.stringify(selectedAttrQuery),
        }).toString()}`;
    })();

    const addToCart = async () => {
        try {
            const response = await axios.post("/cart/add", {
                product_id: product.id,
            });

            if (response.data?.cartCount !== undefined) {
                window.dispatchEvent(
                    new CustomEvent("cart:updated", {
                        detail: { cartCount: response.data.cartCount },
                    }),
                );
            }

            Swal.fire({
                icon: response.data?.type || "success",
                title: response.data?.message || "Product added to cart",
                toast: true,
                timer: 1800,
                showConfirmButton: false,
                position: "bottom-start",
            });
        } catch (error) {
            if (error.response?.status === 401) {
                router.get(route("login"));
            }
        }
    };

    const saveForLater = async () => {
        if (!auth?.user) {
            router.get(route("login"));
            return;
        }

        if (savingForLater) return;

        setSavingForLater(true);

        try {
            const response = await axios.post(
                route("products.save-for-later", {
                    id: product.id,
                    slug: product.slug,
                }),
            );

            const isSaved = Boolean(response.data?.saved);
            setIsSavedForLater(isSaved);
            onSaveForLaterChange?.(product, isSaved);
            Swal.fire({
                icon: "success",
                title:
                    response.data?.message ||
                    (response.data?.saved
                        ? "Product saved for later"
                        : "Product removed from saved list"),
                toast: true,
                timer: 1800,
                showConfirmButton: false,
                position: "bottom-start",
            });
        } catch (error) {
            if (error.response?.status === 401) {
                router.get(route("login"));
                return;
            }

            Swal.fire({
                icon: "error",
                title: "Unable to save product",
                toast: true,
                timer: 1800,
                showConfirmButton: false,
                position: "bottom-start",
            });
        } finally {
            setSavingForLater(false);
        }
    };

    const copyText = async (value) => {
        if (window.navigator?.clipboard?.writeText) {
            await window.navigator.clipboard.writeText(value);
            return;
        }

        const input = document.createElement("input");
        input.value = value;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
    };

    const copyLink = async () => {
        try {
            await copyText(productUrl);
            setCopied(true);
            setTimeout(() => setCopied(false), 1200);
        } catch (error) {
            console.error("Copy failed:", error);
        }
    };

    const scrollThumbnails = (direction) => {
        thumbnailScrollerRef.current?.scrollBy({
            left: direction * 240,
            behavior: "smooth",
        });
    };

    const handleMouseMove = (e) => {
        const img = imageRef.current;

        if (!img) return;

        const rect = img.getBoundingClientRect();
        const lensWidth = 120;
        const lensHeight = 120;
        const zoom = 4;

        let x = e.clientX - rect.left - lensWidth / 2;
        let y = e.clientY - rect.top - lensHeight / 2;

        x = Math.max(0, Math.min(x, rect.width - lensWidth));
        y = Math.max(0, Math.min(y, rect.height - lensHeight));

        setLensPosition({ x, y });
        setBgPosition(`-${x * zoom}px -${y * zoom}px`);
    };

    return (
        <div>
            <style>{`
                .product-zoom-wrapper {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    align-items: flex-start;
                }

                .image-area {
                    position: relative;
                    width: 100%;
                    border: 1px solid #eee;
                    background: #fff;
                }

                @media (max-width: 768px) {
                    .image-area {
                        width: 100%;
                    }
                }

                .product-thumbnail-strip {
                    scrollbar-width: none;
                }

                .product-thumbnail-strip::-webkit-scrollbar {
                    display: none;
                }
            `}</style>

            <div className="relative justify-start p-2 lg:flex item-start">
                <div className="w-full p-3" style={{ maxWidth: "600px" }}>
                    <div className="items-start rounded product-zoom-wrapper">
                        <div
                            className="image-area shrink-0"
                            onMouseEnter={() => setIsZooming(true)}
                            onMouseLeave={() => setIsZooming(false)}
                            onMouseMove={handleMouseMove}
                        >
                            <img
                                ref={imageRef}
                                className="w-full p-2 bg-white border rounded-md"
                                style={{
                                    objectFit: "contain",
                                    height: "420px",
                                    maxWidth: "600px"
                                }}
                                src={`/storage/${previewImage}`}
                                alt={product.title}
                            />

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
                            <div className="relative flex items-center justify-center w-full gap-2">
                                {gallery.length > 5 ? (
                                    <button
                                        type="button"
                                        onClick={() => scrollThumbnails(-1)}
                                        className="z-10 flex items-center justify-center w-8 h-16 bg-white border rounded shadow-sm shrink-0 hover:bg-gray-50"
                                    >
                                        <i className="fas fa-angle-left"></i>
                                    </button>
                                ) : null}

                                <div
                                    ref={thumbnailScrollerRef}
                                    className="flex max-w-[280px] gap-2 overflow-x-auto product-thumbnail-strip scroll-smooth"
                                >
                                    {gallery.map((item) => (
                                        <button
                                            key={`${item.type}-${item.value}`}
                                            type="button"
                                            className={`flex h-16 w-16 shrink-0 items-center justify-center rounded border bg-white p-1 ${
                                                item.type === "image" && item.value === previewImage
                                                    ? "border-orange-500"
                                                    : "border-gray-200"
                                            }`}
                                            onClick={() => {
                                                if (item.type === "video") {
                                                    setShowVideoModal(true);
                                                    return;
                                                }

                                                setPreviewImage(item.value);
                                            }}
                                        >
                                            {item.type === "video" ? (
                                                <div
                                                    className="relative flex items-center justify-center w-full h-full overflow-hidden rounded bg-slate-900"
                                                >
                                                    <div className="absolute inset-0 bg-black/80" />
                                                    <span className="relative z-10 flex items-center justify-center w-8 h-8 text-white rounded-full bg-black/60">
                                                        <i className="text-xs fas fa-play"></i>
                                                    </span>
                                                </div>
                                            ) : (
                                                <img
                                                    className="object-cover w-full h-full rounded"
                                                    src={`/storage/${item.value}`}
                                                    alt={product.title}
                                                />
                                            )}
                                        </button>
                                    ))}
                                </div>

                                {gallery.length > 5 ? (
                                    <button
                                        type="button"
                                        onClick={() => scrollThumbnails(1)}
                                        className="z-10 flex items-center justify-center w-8 h-16 bg-white border rounded shadow-sm shrink-0 hover:bg-gray-50"
                                    >
                                        <i className="fas fa-angle-right"></i>
                                    </button>
                                ) : null}
                            </div>
                        ) : null}
                    </div>
                </div>

                <div className="relative w-full p-3" style={{ minWidth: "300px" }}>
                    <div
                        style={{
                            position: "absolute",
                            top: "20px",
                            right: 0,
                            width: "100%",
                            height: "420px",
                            border: "1px solid #eee",
                            backgroundRepeat: "no-repeat",
                            backgroundColor: "#fff",
                            display:
                                isZooming && typeof window !== "undefined" && window.innerWidth > 768
                                    ? "block"
                                    : "none",
                            zIndex: 9999,
                            boxShadow: "0 8px 24px rgba(0,0,0,.15)",
                            backgroundImage: `url('/storage/${previewImage}')`,
                            backgroundSize: "1680px 1680px",
                            backgroundPosition: bgPosition,
                        }}
                    />

                    <div className="flex flex-wrap items-center w-full gap-3 text-sm text-green-900">
                        {product?.owner?.shop?.id ? (
                            <NavLink
                                href={route("shops.visit", {
                                    id: product.owner.shop.id,
                                    name: product.owner.shop.shop_name_en,
                                })}
                                className="px-2 pt-0 border-b-0 rounded-xl bg-gray-50 text-inherit hover:text-inherit hover:border-transparent"
                            >
                                <strong>
                                    {product?.owner?.shop?.shop_name_en ??
                                        "N/A"}
                                </strong>
                            </NavLink>
                        ) : null}

                        {whatsappPhone ? (
                            <a
                                href={`https://wa.me/${whatsappPhone}`}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-block px-2 py-1 rounded-xl bg-gray-50 hover:bg-green-50"
                                title="Chat on WhatsApp"
                            >
                                <i className="mr-2 fab fa-whatsapp"></i>
                                <span>{rawPhone}</span>
                            </a>
                        ) : null}
                    </div>

                    <div
                        style={{ fontSize: "28px", fontWeight: "bold" }}
                        className="capitalize"
                    >
                        {product.title}
                    </div>

                    <div
                        className="flex items-center justify-between py-2"
                        style={{ fontSize: "14px" }}
                    >
                        <div className="flex items-center gap-1">
                            <RatingStars rating={product?.rating?.average ?? 0} />
                            <div className="px-1" style={{ color: "#737272" }}>
                                {product?.rating?.average ?? 0}/5
                            </div>
                        </div>

                        <button
                            type="button"
                            className="flex items-center cursor-pointer disabled:cursor-not-allowed"
                            onClick={saveForLater}
                            disabled={savingForLater}
                        >
                            <i
                                style={{
                                    color: isSavedForLater
                                        ? "var(--brand-primary)"
                                        : "#ff8a4c",
                                }}
                                className={`mr-2 ${isSavedForLater ? "fas" : "far"} fa-heart`}
                            ></i>
                            <div>
                                {isSavedForLater
                                    ? "saved for you"
                                    : savingForLater
                                      ? "saving..."
                                      : "save for later"}
                            </div>
                        </button>
                    </div>

                    <div className="flex items-center text-sm">
                        <div className="rounded text_primary bold">
                            <NavLink
                                href={route("category.products", {
                                    cat: product?.category?.slug,
                                })}
                                className="p-0 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                            >
                                {product?.category?.name ?? "Undefined"}
                            </NavLink>
                        </div>
                    </div>

                    <div className="bg-gray-50">
                        <Hr />
                        <i className="px-2 fas fa-comments"></i>
                        {product?.rating?.count ?? product?.comments?.length ?? 0} Reviews.
                        <Hr />
                    </div>

                    <ProductAttributesSelector
                        product={product}
                        selectedAttrs={selectedAttrs}
                        onChange={setSelectedAttrs}
                    />

                    {product.shipping_note ? (
                        <div className="flex p-1 bg-indigo-900 rounded-lg shadow bg-gray-50">
                            <i className="h-auto p-2 rounded shadow-xl bg-gray-50 fas fa-bell"></i>
                            <p className="p-2 text-xs text-white">
                                {product.shipping_note}
                            </p>
                        </div>
                    ) : null}

                    <div className="py-3">
                        {product.offer_type ? (
                            <div>
                                <div style={{ fontSize: "20px" }}>
                                    Price :{" "}
                                    <strong className="text_secondary">
                                        {product.discount} TK
                                    </strong>
                                </div>
                                <div className="flex items-baseline justify-start">
                                    <del className="text-sm">
                                        MRP: {product.price} TK
                                    </del>
                                    <div className="px-2 text-xs">
                                        {discountPercentage}% OFF
                                    </div>
                                </div>
                            </div>
                        ) : (
                            <div
                                style={{ fontSize: "22px" }}
                                className="font-bold text_primary"
                            >
                                Price : {product.price} TK
                            </div>
                        )}
                    </div>

                    <Hr />
                    <div className="flex items-center justify-start w-full space-x-2 purchase-info">
                        {onBuyNowClick ? (
                            <button
                                type="button"
                                onClick={onBuyNowClick}
                                className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-orange-500 border border-transparent rounded-md hover:text-white hover:border-transparent"
                            >
                                Buy Now <i className="fas fa-arrow-right ms-2"></i>
                            </button>
                        ) : (
                            <Link
                                href={buyNowHref}
                                className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-orange-500 border border-transparent rounded-md hover:text-white hover:border-transparent"
                            >
                                Buy Now <i className="fas fa-arrow-right ms-2"></i>
                            </Link>
                        )}
                        <SecondaryButton type="button" onClick={addToCart} className="py-2 space-x-2">
                            <i className="fas fa-cart-plus"></i>
                            <span className="hidden md:block">Add to Cart</span>
                        </SecondaryButton>
                    </div>

                    <div className="mt-8 text-xs">
                        <div className="mb-2 font-semibold">SHARE WITH YOUR FRIENDS</div>
                        <div className="flex items-center gap-3">
                            <a
                                href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
                                    productUrl,
                                )}`}
                                title="Facebook"
                                target="_blank"
                                rel="noreferrer"
                                style={{ backgroundColor: "#1877F2" }}
                                className="flex items-center justify-center w-10 h-10 text-2xl text-white rounded-full"
                            >
                                <i className="fab fa-facebook-f"></i>
                            </a>
                            <a
                                href={`https://twitter.com/intent/tweet?url=${encodeURIComponent(
                                    productUrl,
                                )}`}
                                title="Twitter"
                                target="_blank"
                                rel="noreferrer"
                                style={{ backgroundColor: "#1DA1F2" }}
                                className="flex items-center justify-center w-10 h-10 text-2xl text-white rounded-full"
                            >
                                <i className="fab fa-twitter"></i>
                            </a>
                            <a
                                href={`https://wa.me/?text=${encodeURIComponent(
                                    productUrl,
                                )}`}
                                title="WhatsApp"
                                target="_blank"
                                rel="noreferrer"
                                style={{ backgroundColor: "#25D366" }}
                                className="flex items-center justify-center w-10 h-10 text-2xl text-white rounded-full"
                            >
                                <i className="fab fa-whatsapp"></i>
                            </a>
                        </div>
                        <div className="mt-8">
                            <button
                                type="button"
                                className="flex items-center justify-center px-4 py-2 text-white transition rounded w-36"
                                style={{ background: "#4f4f4f" }}
                                onClick={copyLink}
                            >
                                <i className="mr-2 fas fa-link"></i>
                                {copied ? "Copied" : "Copy Link"}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {relatedProduct?.length > 0 && (
                <>
                    <hr />
                    <div className="p-3 sm:w-full">
                        <div className="font-bold">Related Products</div>
                        <br />
                        <div
                            className="product_section"

                        >
                            <ProductsLoop products={relatedProduct} />
                        </div>
                    </div>
                </>
            )}

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
                            className="w-full bg-black rounded-lg aspect-video"
                        />
                    </div>
                </div>
            ) : null}
        </div>
    );
}
