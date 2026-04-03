import { Link, router } from "@inertiajs/react";
import { useRef, useState } from "react";
import axios from "axios";
import SecondaryButton from "../SecondaryButton";
import Hr from "../Hr";
import ProductsLoop from "./ProductsLoop";
import Swal from "sweetalert2";

export default function ProductSingle({ product, relatedProduct = [] }) {
    const [copied, setCopied] = useState(false);
    const [previewImage, setPreviewImage] = useState(product?.thumbnail);
    const [isZooming, setIsZooming] = useState(false);
    const [lensPosition, setLensPosition] = useState({ x: 0, y: 0 });
    const [bgPosition, setBgPosition] = useState("0px 0px");
    const imageRef = useRef(null);

    if (!product) return null;

    const attrValues = product?.attr?.value
        ? String(product.attr.value)
              .split(",")
              .map((v) => v.trim())
              .filter(Boolean)
        : [];

    const gallery = [...new Set([
        product.thumbnail,
        ...(product?.showcase?.map((image) => image?.image) ?? []),
    ].filter(Boolean))];

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

    const addToCart = async () => {
        try {
            const response = await axios.post("/cart/add", {
                product_id: product.id,
            });
            router.reload({ only: ["auth"] });
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
                window.location.href = route("login");
            }
        }
    };

    const copyLink = async () => {
        await navigator.clipboard.writeText(productUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 1200);
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
                    gap: 20px;
                    align-items: flex-start;
                }

                .image-area {
                    position: relative;
                    width: 420px;
                    border: 1px solid #eee;
                    background: #fff;
                }

                @media (max-width: 768px) {
                    .image-area {
                        width: 100%;
                    }
                }
            `}</style>

            <div className="relative justify-start p-2 lg:flex item-start">
                <div className="w-full p-3" style={{ maxWidth: "600px" }}>
                    <div className="items-start rounded product-zoom-wrapper sm:flex sm:justify-start lg:block">
                        <div
                            className="image-area"
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
                            <div className="flex flex-wrap items-center md:block lg:flex">
                                {gallery.map((image) => (
                                    <button
                                        key={image}
                                        type="button"
                                        className="p-1 mb-1 rounded"
                                        onClick={() => setPreviewImage(image)}
                                    >
                                        <img
                                            className="p-1 border rounded"
                                            src={`/storage/${image}`}
                                            width="60"
                                            height="60"
                                            alt={product.title}
                                        />
                                    </button>
                                ))}
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
                            <Link
                                href={route("shops.visit", {
                                    id: product.owner.shop.id,
                                    name: product.owner.shop.shop_name_en,
                                })}
                                className="px-2 rounded-xl bg-gray-50"
                            >
                                <strong>
                                    {product?.owner?.shop?.shop_name_en ??
                                        "N/A"}
                                </strong>
                            </Link>
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
                        <div className="flex items-center">
                            <i className="fas fa-star text_primary"></i>
                            <i className="fas fa-star text_primary"></i>
                            <i className="fas fa-star text_primary"></i>
                            <i
                                style={{ color: "#737272" }}
                                className="fas fa-star"
                            ></i>
                            <i
                                style={{ color: "#737272" }}
                                className="fas fa-star"
                            ></i>
                            <div className="px-1" style={{ color: "#737272" }}>
                                7/10
                            </div>
                        </div>

                        <div className="flex items-center cursor-pointer">
                            <i
                                style={{ color: "var(--brand-primary)" }}
                                className="mr-2 fas fa-heart"
                            ></i>
                            <div>save for later</div>
                        </div>
                    </div>

                    <div className="flex items-center text-sm">
                        <div className="rounded text_primary bold">
                            <Link
                                href={route("category.products", {
                                    cat: product?.category?.slug,
                                })}
                            >
                                {product?.category?.name ?? "Undefined"}
                            </Link>
                        </div>
                    </div>

                    <div className="bg-gray-50">
                        <Hr />
                        <i className="px-2 fas fa-comments"></i>
                        {product?.comments?.length ?? 0} Reviews.
                        <Hr />
                    </div>

                    {attrValues.length > 0 && (
                        <div className="py-2 my-3">
                            <h4>{product?.attr?.name}</h4>
                            <div className="flex flex-wrap items-center justify-start gap-2 my-1">
                                {attrValues.map((attr) => (
                                    <div
                                        key={attr}
                                        className="px-2 py-1 text-sm text-white bg-indigo-300 rounded"
                                    >
                                        {attr.toUpperCase()}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

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
                        <Link
                            href={route("product.makeOrder", { id: product.id, slug: product.slug })}
                            className="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-orange-500 border border-transparent rounded-md"
                        >
                            Buy Now <i className="fas fa-arrow-right ms-2"></i>
                        </Link>
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
        </div>
    );
}
