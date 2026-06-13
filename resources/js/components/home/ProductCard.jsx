import { Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import axios from "axios";
import Swal from "sweetalert2";
import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";
import { formatAmount } from "../../utils/formatAmount";

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
    const hasOffer = product.offer_type && product.discount;

    const discountPercentage = hasOffer
        ? Math.round(((product.price - product.discount) / product.price) * 100)
        : null;

    const isSoldOut = product.unit < 2;


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
        <div className="relative overflow-hidden bg-white border box group">
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
            <div className="absolute inset-0 hidden transition-opacity opacity-0 option_container lg:block bg-orange-100/40 group-hover:opacity-100">
                <div className="flex flex-col items-center justify-between w-full h-full">
                    <div className="flex flex-col justify-center flex-1 w-full text-center">
                        <button
                            onClick={addToCart}
                            className="w-full p-2 mb-4 text-sm bg-white"
                        >
                            <i className="mx-2 fas fa-cart-plus"></i>
                            {t("To Cart")}
                        </button>

                       <Link
                            href={`/product/${product.id}/${product.slug}`}
                            className="text-xs"
                        >
                            {t("View Details")}
                            <i className="mx-2 fas fa-arrow-right"></i>
                        </Link>
                    </div>

                    <NavLink
                        href={route("product.makeOrder", { id: product.id, slug: product.slug })}
                        className="flex items-center justify-center w-full py-2 font-bold text-center bg-white border-b-0 text_primary hover:bg-white hover:border-transparent"
                    >
                        {t("Order Now")}
                        <i className="mx-2 fas fa-arrow-right"></i>
                    </NavLink>
                </div>
            </div>

            {/* Image */}
            <div className="overflow-hidden img-box">
                <img
                    src={`/storage/${product.thumbnail}`}
                    className="object-cover w-full h-40 transition-transform duration-300 group-hover:scale-125"
                    alt={product.title}
                />
            </div>

            {/* Card Body */}
            <div className="flex flex-col justify-between p-2 h-28">
                {/* Title + Unit */}
                <div className="flex items-start justify-between space-x-1 text-white">
                    <NavLink
                        href={route("products.details", { id: product.id, slug: product.slug })}
                        className="block w-full p-1 text-xs text-white truncate border-b-0 bg_primary hover:text-white hover:border-transparent"
                    >
                        {product.title.length > 15
                            ? product.title.substring(0, 15) + "..."
                            : product.title}
                    </NavLink>

                    <div className="h-full p-1 text-xs bg_primary">
                        {product.unit ?? 0}
                    </div>
                </div>

                {/* Price Section */}
                <div className="flex items-center justify-between py-1 text-sm font-bold">
                    {hasOffer ? (
                        <>
                            <span className="text-md">
                                {formatAmount(product.discount)} {t("TK")}
                            </span>

                            <span className="text-xs">
                                <del>{t("MRP")} {formatAmount(product.price)} {t("TK")}</del>
                            </span>
                        </>
                    ) : (
                        <span>{formatAmount(product.price)} {t("TK")}</span>
                    )}
                </div>

                {/* Order Button */}
                <NavLink
                    href={route("product.makeOrder", { id: product.id, slug: product.slug })}
                    className="flex items-center justify-center block text-sm font-bold text-center transition bg-white border-b-0 text_primary hover:bg_primary hover:text-white hover:border-transparent"
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
