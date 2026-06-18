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

            Swal.fire({
                icon: isAlreadyInCart ? "info" : "success",
                title: isAlreadyInCart ? t("Look At!") : t("Congrass !"),
                text: response.data?.message || t("Product Added to cart"),
                confirmButtonText: t("OK"),
                confirmButtonColor: "#6c5ce7",
            }).then(() => {
                if (!isAlreadyInCart && response.data.cartCount !== undefined) {
                    router.reload({ only: ["auth"] });
                }
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
        <div className="relative flex h-[296px] flex-col overflow-hidden rounded-md border bg-white p-0 shadow-sm group">
            {/* Discount Badge */}
            {hasOffer && (
                <div className="absolute top-0 left-0 z-10 px-2 py-1 text-xs text-white discount-badge bg_primary">
                    {discountPercentage}%
                </div>
            )}

            {showSaveForLater ? (
                <button
                    type="button"
                    className="absolute top-2 right-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm shadow disabled:opacity-70"
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
            <div className="pointer-events-none absolute inset-x-0 top-0 z-30 hidden h-[190px] bg-white/80 opacity-0 transition-opacity duration-200 lg:flex lg:items-center lg:justify-center group-hover:opacity-100">
                <div className="pointer-events-auto flex w-full flex-col items-center justify-center text-center">
                    <button
                        type="button"
                        onClick={addToCart}
                        className="w-full bg-white p-2 text-sm text-black transition hover:bg-white hover:text-black"
                    >
                        <i className="mx-2 fas fa-cart-plus"></i>
                        {t("To Cart")}
                    </button>

                    <Link
                        href={route("products.details", { id: product.id, slug: product.slug })}
                        className="w-full bg-gray-100 p-2 text-xs text-black transition hover:bg-gray-100 hover:text-black"
                    >
                        {t("View Details")}
                        <i className="mx-2 fas fa-arrow-right"></i>
                    </Link>
                </div>
            </div>

            {/* Image */}
            <div className="h-40 shrink-0 overflow-hidden bg-gray-50">
                <img
                    src={`/storage/${product.thumbnail}`}
                    className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-125"
                    alt={product.title}
                />
            </div>

            {/* Card Body */}
            <div className="flex min-h-0 flex-1 flex-col justify-between p-2">
                {/* Title + Unit */}
                <div className="flex h-8 items-stretch gap-1 text-white">
                    <NavLink
                        href={route("products.details", { id: product.id, slug: product.slug })}
                        className="flex min-w-0 flex-1 items-center border-b-0 bg_primary px-1 text-xs text-white hover:border-transparent hover:text-white"
                    >
                        <span className="block truncate">
                            {product.title}
                        </span>
                    </NavLink>

                    <div className="flex w-9 shrink-0 items-center justify-center bg_primary px-1 text-xs">
                        <span className="block max-w-full truncate">
                            {product.unit ?? 0}
                        </span>
                    </div>
                </div>

                {/* Price Section */}
                <div className="flex h-10 items-center justify-between gap-2 overflow-hidden py-1 text-sm font-bold">
                    {hasOffer ? (
                        <>
                            <span className="min-w-0 truncate text-sm">
                                {formatCurrency(product.discount)}
                            </span>

                            <span className="min-w-0 shrink-0 truncate text-xs">
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
                    className="flex h-9 items-center justify-center border-b-0 bg-white text-center text-sm font-bold text_primary transition hover:border-transparent hover:bg_primary hover:text-white"
                >
                    <i className="mr-2 fas fa-cart-plus"></i>
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
