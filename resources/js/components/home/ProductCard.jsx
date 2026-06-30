import { Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import axios from "axios";
import Swal from "sweetalert2";
import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";
import { formatCurrency } from "../../utils/formatAmount";

export default function ProductCard({
    product,
    showSaveForLater = false,
    savedForLater = false,
    onSaveForLaterChange = null,
}) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const [isSaved, setIsSaved] = useState(Boolean(savedForLater));
    const [saving, setSaving] = useState(false);
    const price = Number(product.price ?? 0);
    const discount = Number(product.discount ?? 0);
    const hasOfferType = [true, 1, "1", "yes", "Yes", "YES"].includes(product.offer_type);
    const hasOffer = hasOfferType && discount > 0 && price > discount;

    const discountPercentage = hasOffer
        ? Math.round(((price - discount) / price) * 100)
        : null;

    const stock = Number.parseFloat(product.unit);
    const isSoldOut = Number.isFinite(stock) && stock < 1;


    const addToCart = async () => {
        try {
            const response = await axios.post("/cart/add", {
                product_id: product.id,
            });

            const responseType = response.data?.type || "success";
            const isAlreadyInCart = responseType === "info";

            if (response.data?.cartCount !== undefined) {
                window.dispatchEvent(
                    new CustomEvent("cart:updated", {
                        detail: { cartCount: response.data.cartCount },
                    }),
                );
            }

            Swal.fire({
                icon: isAlreadyInCart ? "info" : "success",
                title: isAlreadyInCart ? t("Look At!") : t("Congrass !"),
                text: response.data?.message || t("Product Added to cart"),
                confirmButtonText: t("OK"),
                confirmButtonColor: "#6c5ce7",
            });
        } catch (error) {
            if (error.response?.status === 401) {
                Swal.fire({
                    icon: "warning",
                    title: t("Alert !"),
                    text: t("Login to add Cart"),
                    confirmButtonText: t("OK"),
                    confirmButtonColor: "#6c5ce7",
                }).then(() => {
                    router.get(route("login"));
                });
            }
        }
    };

    const toggleSaveForLater = async () => {
        if (!auth?.user) {
            router.get(route("login"));
            return;
        }

        if (saving) return;

        setSaving(true);

        try {
            const response = await axios.post(
                route("products.save-for-later", {
                    id: product.id,
                    slug: product.slug,
                }),
            );
            const saved = Boolean(response.data?.saved);

            setIsSaved(saved);
            onSaveForLaterChange?.(product, saved);

            Swal.fire({
                icon: "success",
                title:
                    response.data?.message ||
                    (saved
                        ? t("Product saved for later")
                        : t("Product removed from saved list")),
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
                title: t("Unable to update saved product"),
                toast: true,
                timer: 1800,
                showConfirmButton: false,
                position: "bottom-start",
            });
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="relative flex h-[248px] flex-col overflow-hidden rounded-md border bg-white p-0 shadow-sm group sm:h-[296px]">
            {/* Discount Badge */}
            {hasOffer && (
                <div className="absolute top-0 left-0 z-10 px-2 py-1 text-xs text-white discount-badge bg_primary">
                    {discountPercentage}%
                </div>
            )}

            {showSaveForLater ? (
                <button
                    type="button"
                    className="absolute z-30 flex items-center justify-center w-8 h-8 text-sm bg-white rounded-full shadow top-2 right-2 disabled:opacity-70"
                    onClick={toggleSaveForLater}
                    disabled={saving}
                    title={isSaved ? t("Remove from For You") : t("Save for later")}
                >
                    <i
                        className={`${isSaved ? "fas" : "far"} fa-heart`}
                        style={{ color: "var(--brand-primary)" }}
                    ></i>
                </button>
            ) : null}

            {/* Hover Option Container */}
            <div className="pointer-events-none absolute inset-x-0 top-0 z-30 hidden h-[150px] bg-white/80 opacity-0 transition-opacity duration-200 sm:h-[190px] lg:flex lg:items-center lg:justify-center group-hover:opacity-100">
                <div className="flex flex-col items-center justify-center w-full text-center pointer-events-auto">
                    <button
                        type="button"
                        onClick={addToCart}
                        className="w-full p-2 text-sm text-black transition bg-white hover:bg-white hover:text-black"
                    >
                        <i className="mx-2 fas fa-cart-plus"></i>
                        {t("To Cart")}
                    </button>

                    <Link
                        href={route("products.details", { id: product.id, slug: product.slug })}
                        className="w-full p-2 text-xs text-black transition bg-gray-100 hover:bg-gray-100 hover:text-black"
                    >
                        {t("View Details")}
                        <i className="mx-2 fas fa-arrow-right"></i>
                    </Link>
                </div>
            </div>

            {/* Image */}
            <div className="h-32 overflow-hidden shrink-0 bg-gray-50 sm:h-40">
                <img
                    src={`/storage/${product.thumbnail}`}
                    className="object-cover w-full h-full transition-transform duration-300 group-hover:scale-125"
                    alt={product.title}
                />
            </div>

            {/* Card Body */}
            <div className="flex flex-col justify-between flex-1 min-h-0 p-2">
                {/* Title + Unit */}
                <div className="flex items-stretch h-8 gap-1 text-white">
                    <NavLink
                        href={route("products.details", { id: product.id, slug: product.slug })}
                        className="flex min-w-0 flex-1 items-center border-b-0 bg_primary px-1 text-[11px] text-white hover:border-transparent hover:text-white sm:text-xs"
                    >
                        <span className="block truncate">
                            {product.title}
                        </span>
                    </NavLink>

                    <div className="flex w-8 shrink-0 items-center justify-center bg_primary px-1 text-[11px] sm:w-9 sm:text-xs">
                        <span className="block max-w-full truncate">
                            {product.unit ?? 0}
                        </span>
                    </div>
                </div>

                {/* Price Section */}
                <div className="flex items-center justify-between h-10 gap-1 py-1 overflow-hidden text-xs font-bold sm:gap-2 sm:text-sm">
                    {hasOffer ? (
                        <>
                            <span className="text-xs sm:text-sm">
                                {formatCurrency(product.discount)}
                            </span>

                            <span className="text-gray-400 line-through sm:text-xs leading-1">
                                <del>{t("MRP")} {formatCurrency(product.price)}</del>
                            </span>
                        </>
                    ) : (
                        <span className="truncate">{formatCurrency(product.price)}</span>
                    )}
                </div>

                {/* Order Button */}
                <NavLink
                    href={route("product.makeOrder", { id: product.id, slug: product.slug })}
                    className="flex items-center justify-center h-8 text-xs font-bold text-center transition bg-white border-b-0 text_primary hover:border-transparent hover:bg_primary hover:text-white sm:h-9 sm:text-sm"
                >
                    <i className="mr-1 fas fa-cart-plus sm:mr-2"></i>
                    {t("Order Now")}
                </NavLink>
            </div>

            {/* Sold Out Overlay */}
            {isSoldOut && (
                <div className="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full bg-black/30">
                    <div className="w-full py-1 text-sm font-bold text-center uppercase bg-white">
                        {t("Sold Out")}
                    </div>
                </div>
            )}
        </div>
    );
}
