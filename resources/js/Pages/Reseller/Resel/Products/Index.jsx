import { Head, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";
import InputField from "../../../../components/InputField";
import InputFile from "../../../../components/InputFile";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import useTranslation from "../../../../hooks/useTranslation";
import DistrictUpozilaSelect from "../../../../components/DistrictUpozilaSelect";

function CategoryTree({ categories = [], activeCat, search = "", t }) {
    const normalizedSearch = search.trim().toLowerCase();

    const matchesCategory = (category) => {
        if (!normalizedSearch) {
            return true;
        }

        if ((category.name ?? "").toLowerCase().includes(normalizedSearch)) {
            return true;
        }

        return (category.children ?? []).some((child) => matchesCategory(child));
    };

    return categories
        .filter((item) => item.slug !== "default-category")
        .filter((item) => matchesCategory(item))
        .map((item) => (
            <div
                key={item.id}
                className="p-2 border-b border-gray-200 cursor-pointer hover:bg-gray-50"
            >
                <div>
                    <NavLink
                        active={String(activeCat) === String(item.id)}
                        href={route("reseller.resel-product.index", {
                            cat: item.id,
                            search: search || undefined,
                        })}
                    >
                        {item.name}
                    </NavLink>

                    <div>
                        {(item.children ?? []).length > 0 ? (
                            <div className="px-2 py-1 border-l">
                                {item.children.map((child) => (
                                    <div key={child.id}>
                                        <NavLink
                                            active={
                                                String(activeCat) ===
                                                String(child.id)
                                            }
                                            href={route(
                                                "reseller.resel-product.index",
                                                {
                                                    cat: child.id,
                                                    search: search || undefined,
                                                }
                                            )}
                                        >
                                            {child.name}
                                        </NavLink>

                                        <div className="ps-2">
                                            {(child.children ?? []).map(
                                                (sc) => (
                                                    <NavLink
                                                        key={sc.id}
                                                        active={
                                                            String(activeCat) ===
                                                            String(sc.id)
                                                        }
                                                        href={route(
                                                            "reseller.resel-product.index",
                                                            {
                                                                cat: sc.id,
                                                                search: search || undefined,
                                                            }
                                                        )}
                                                    >
                                                        {sc.name}
                                                    </NavLink>
                                                )
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <span className="text-sm text-gray-500">{t("No subcategories")}</span>
                        )}
                    </div>
                </div>
            </div>
        ));
}

function ProductCard({ product, onPurchase, t }) {
    const discountPercent = useMemo(() => {
        if (!product.offer_type || !product.price) return 0;
        const diff = product.price - (product.discount ?? 0);
        return Math.round(((diff / product.price) * 100) * 10) / 10;
    }, [product]);

    return (
        <div className="relative flex h-full flex-col overflow-hidden bg-white rounded shadow">
            {product.offer_type ? (
                <div className="bg-orange-500 discount-badge">
                    {discountPercent}%</div>
            ) : null}

            <div className="p-1 overflow-hidden shadow-md">
                {product.thumbnail_url ? (
                    <img
                        style={{ height: "120px" }}
                        src={product.thumbnail_url}
                        className="object-cover w-full sm:h-36"
                        alt="image"
                    />
                ) : null}
            </div>

            <div className="flex flex-1 flex-col justify-between p-2 bg-white">
                <NavLink
                    href={route("reseller.resel-product.veiw", {
                        pd: product.id,
                    })}
                >
                    <div className="text-sm product-title-clamp-3">
                        {product.name ?? "N/A"}
                    </div>
                </NavLink>

                <div>
                    <div className="mb-3 text-md">
                        {product.offer_type ? (
                            <>
                                <div className="bold">
                                    {product.discount ?? "0"}{t("TK")}</div>
                                <div className="text-xs">
                                    <del>{product.price ?? "0"} TK</del>
                                </div>
                            </>
                        ) : (
                            <div className="bold">
                                {product.price ?? "0"}{t("TK")}</div>
                        )}
                    </div>

                    <div className="flex items-center justify-center text-sm">
                        <Hr />
                        <PrimaryButton
                            type="button"
                            className="flex w-full justify-between text-center"
                            onClick={() => onPurchase(product)}
                        >{t("Purchase")}{" "}
                            <i className="pl-2 fas fa-angle-right"></i>
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Index({
    products,
    categories = [],
    filters = {},
    shop,
    totalReselProducts = 0,
    ableToAdd = false,
}) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const [showOrderModal, setShowOrderModal] = useState(false);
    const [activeProduct, setActiveProduct] = useState(null);
    const [search, setSearch] = useState(filters.search ?? "");
    const [categorySearch, setCategorySearch] = useState("");

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

    const openOrderModal = (product) => {
        setActiveProduct(product);
        orderForm.reset();
        setShowOrderModal(true);
    };

    const closeOrderModal = () => {
        setShowOrderModal(false);
        setActiveProduct(null);
    };

    const submitOrder = (e) => {
        e.preventDefault();
        if (!activeProduct) return;

        orderForm.post(
            route("reseller.resel-product.order", {
                product: activeProduct.id,
            }),
            {
                onSuccess: () => {
                    closeOrderModal();
                },
            }
        );
    };

    const cleanLabel = (label) =>
        String(label)
            .replace(/&laquo;/g, "")
            .replace(/&raquo;/g, "")
            .trim();

    const requestProducts = (overrides = {}, options = {}) => {
        const nextSearch = overrides.search ?? search.trim();

        router.get(
            route("reseller.resel-product.index"),
            {
                cat: overrides.cat ?? filters.cat ?? undefined,
                search: nextSearch || undefined,
                page: overrides.page ?? undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                ...options,
            }
        );
    };

    const pagination = useMemo(() => {
        const links = products?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [products?.links]);

    const quantityOptions = useMemo(() => {
        if (!activeProduct?.unit || Number(activeProduct.unit) < 1) {
            return [];
        }
        return Array.from({ length: Number(activeProduct.unit) }, (_, i) => i + 1);
    }, [activeProduct]);

    const attrOptions = useMemo(() => {
        const value = activeProduct?.attr?.value ?? "";
        if (!value) return [];
        return value.split(",").map((item) => item.trim()).filter(Boolean);
    }, [activeProduct]);

    const totalPrice =
        (Number(orderForm.data.quantity || 0) || 0) *
        Number(activeProduct?.total_price || 0);

    useEffect(() => {
        setSearch(filters.search ?? "");
    }, [filters.search]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            requestProducts({ search: trimmedSearch });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, filters.search]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url, window.location.origin);

        requestProducts({
            cat: nextUrl.searchParams.get("cat") ?? filters.cat,
            search: nextUrl.searchParams.get("search") ?? search.trim(),
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const resultSummary =
        products?.total > 0
            ? `Showing ${products?.from ?? 0}-${products?.to ?? 0} of ${products?.total ?? 0} products`
            : "No products found";

    return (
        <AppLayout
            title={t("Resel Products")}
            header={
                <PageHeader>
                    <div className="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>{t("Resel Products")}<br />
                            <div className="flex flex-wrap gap-x-4 gap-y-2">
                                <NavLink
                                    href={route("reseller.resel-product.index")}
                                    active={route().current(
                                        "reseller.resel-product.*"
                                    )}
                                >{t("Product")}</NavLink>
                            </div>
                        </div>

                        <div className="self-start lg:self-auto">
                            <div className="flex overflow-hidden bg-indigo-900 border border-indigo-900 rounded-xl">
                                <div
                                    className="px-3 py-1 bg-white"
                                    title={t("Total Resell Products")}
                                >
                                    {totalReselProducts}
                                </div>
                                <div
                                    className="px-3 py-1 text-white"
                                    title={t("Max Resell Products")}
                                >
                                    {shop?.max_resell_product ?? 0}
                                </div>
                            </div>
                        </div>
                    </div>
                </PageHeader>
            }
        >
            <Head title="Resel Products" />
            <Container>
                {!ableToAdd ? (
                    <div className="p-2 text-red-800 bg-red-200">{t("You have reached the maximum number of products you can upload")}{shop?.max_resell_product ?? 0}{t(". Please delete some products to add new ones or upgrade your plan.")}</div>
                ) : null}

                <div>
                    <div>
                        <div className="flex flex-col gap-2 py-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                            <button
                                type="button"
                                onClick={() => setShowCategoryModal(true)}
                                className="flex items-center justify-between gap-3 px-3 py-2 text-sm border rounded-md hover:bg-white sm:w-auto"
                            >
                                <span>{t("Categories")}</span>
                                <i className="fas fa-angle-right"></i>
                            </button>
                            <TextInput
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) => {
                                    if (e.key !== "Enter") {
                                    return;
                                }

                                e.preventDefault();
                                requestProducts({ search: search.trim() });
                            }}
                                className="w-full py-1 sm:w-80"
                                placeholder={t("Search products...")}
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                            {(products?.data ?? []).map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    onPurchase={openOrderModal}
                                    t={t}
                                />
                            ))}
                        </div>
                    </div>

                    {(products?.data ?? []).length < 1 ? (
                        <div className="h-auto p-2 bg-gray-200">{t("No Products Found !")}</div>
                    ) : null}

                    {pagination.pages.length ? (
                        <div className="w-full pt-4">
                            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div className="text-sm text-slate-700">
                                    {resultSummary}
                                </div>
                                <div className="w-full overflow-x-auto lg:w-auto">
                                    <div className="overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200">
                                        <button
                                            type="button"
                                            disabled={!pagination.prev?.url}
                                            className="px-4 py-2 text-sm transition border-r border-slate-200 text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                            onClick={() => goToPage(pagination.prev?.url)}
                                        >{t("Previous")}</button>
                                        {pagination.pages.map((link, index) => (
                                            <button
                                                key={`${link.label}-${index}`}
                                                type="button"
                                                disabled={!link.url}
                                                className={`min-w-10 border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
                                                    link.active
                                                        ? "bg-slate-100 text-blue-600"
                                                        : "bg-white text-slate-700 hover:bg-slate-50"
                                                } disabled:cursor-not-allowed disabled:opacity-50`}
                                                onClick={() => goToPage(link.url)}
                                            >
                                                {cleanLabel(link.label)}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            disabled={!pagination.next?.url}
                                            className="px-4 py-2 text-sm transition text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                            onClick={() => goToPage(pagination.next?.url)}
                                        >{t("Next")}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : null}
                </div>
            </Container>

            <Modal
                show={showCategoryModal}
                onClose={() => setShowCategoryModal(false)}
            >
                <div className="flex flex-wrap items-center justify-between gap-3 p-3 border-b border-gray-200">
                    <div className="text-base font-medium">{t("Explore Categories")}</div>
                    <div className="flex w-full max-w-sm items-center gap-2 sm:w-auto sm:min-w-[320px]">
                        <TextInput
                            type="search"
                            value={categorySearch}
                            onChange={(e) =>
                                setCategorySearch(e.target.value)
                            }
                            className="w-full py-2 mb-0"
                            placeholder={t("Search categories...")}
                        />
                        <button
                            type="button"
                            onClick={() => setShowCategoryModal(false)}
                            className="flex items-center justify-center w-10 h-10 transition rounded-md text-slate-700 hover:bg-slate-100"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div className="px-3 pb-3">
                    <NavLink
                        href={route("reseller.resel-product.index")}
                    >{t("View All Products")}</NavLink>
                </div>
                <div className="flex-1 p-3 overflow-y-scroll">
                    <CategoryTree
                        categories={categories}
                        activeCat={filters?.cat}
                        search={categorySearch}
                        t={t}
                    />
                </div>
                <hr />
                <div className="w-full p-3 text-end">
                    <SecondaryButton
                        type="button"
                        onClick={() => setShowCategoryModal(false)}
                    >
                        <i className="mr-2 fas fa-times"></i>{t("Close")}</SecondaryButton>
                </div>
            </Modal>

            <Modal
                show={showOrderModal}
                onClose={closeOrderModal}
                maxWidth="md"
            >
                {activeProduct ? (
                    <div>
                        <div className="flex flex-col gap-2 p-3 border-b sm:flex-row sm:items-center sm:justify-between bold">
                            <div>{t("Purchase")}</div>
                            <div className="text-lg bold">
                                {activeProduct.total_price}{t("TK")}</div>
                        </div>
                        <div className="flex flex-col items-start justify-start gap-3 p-5 mb-3 bg-gray-100 sm:flex-row">
                            <div className="flex">
                                {activeProduct.thumbnail_url ? (
                                    <img
                                        src={activeProduct.thumbnail_url}
                                        className="w-12 h-12 mr-3 rounded shadow"
                                        alt=""
                                    />
                                ) : null}
                            </div>
                            <div>
                                <div className="text-lg bold">
                                    {activeProduct.name ?? "N/A"}
                                </div>
                                <div className="text-sm">
                                    {activeProduct.offer_type ? (
                                        <div className="flex items-baseline gap-2">
                                            <div className="bold">{t("Price :")}{" "}
                                                {activeProduct.total_price}{t("TK")}</div>
                                            <div className="text-xs">
                                                <del>
                                                    MRP:{" "}
                                                    {activeProduct.price ?? "0"}{" "}
                                                    TK
                                                </del>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="bold">{t("Price :")}{" "}
                                            {activeProduct.price ?? "0"}{t("TK")}</div>
                                    )}
                                    <div className="text-xs">{t("Available Stock:")}{" "}
                                        {activeProduct.unit ?? "0"}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form onSubmit={submitOrder} className="p-5">
                            <InputField
                                className="md:flex"
                                labelWidth="140px"
                                label={t("Name")}
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
                                label={t("Phone")}
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
                                districtLabel={t("District")}
                                upozilaLabel={t("Upozila")}
                                onDistrictChange={(value) =>
                                    orderForm.setData("district", value)
                                }
                                onUpozilaChange={(value) =>
                                    orderForm.setData("upozila", value)
                                }
                            />
                            <InputFile
                                labelWidth="140px"
                                label={t("Full Address")}
                                name="location"
                                error="location"
                                errors={orderForm.errors}
                            >
                                <textarea
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                    placeholder={t("Full Address")}
                                    value={orderForm.data.location}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "location",
                                            e.target.value
                                        )
                                    }
                                ></textarea>
                            </InputFile>
                            <InputField
                                className="md:flex"
                                labelWidth="140px"
                                label={t("Road No")}
                                name="road_no"
                                inputClass="w-full"
                                error={orderForm.errors.road_no}
                                value={orderForm.data.road_no}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "road_no",
                                        e.target.value
                                    )
                                }
                            />
                            <InputField
                                className="md:flex"
                                labelWidth="140px"
                                label={t("House No")}
                                name="house_no"
                                inputClass="w-full"
                                error={orderForm.errors.house_no}
                                value={orderForm.data.house_no}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "house_no",
                                        e.target.value
                                    )
                                }
                            />

                            <InputFile
                                labelWidth="140px"
                                label={t("Quantity")}
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
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">{t("Select Quantity")}</option>
                                    {quantityOptions.map((qty) => (
                                        <option key={qty} value={qty}>
                                            {qty}
                                        </option>
                                    ))}
                                </select>
                            </InputFile>

                            <div className="p-3 my-3 text-sm rounded-md shadow-sm bg-indigo-50">
                                <div className="text-xs">
                                    {Number(activeProduct.unit) < 1
                                        ? "Stock Out"
                                        : `You can order maximum ${activeProduct.unit} item`}
                                </div>
                                <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div>{t("Total")}</div>
                                    <div>
                                        {orderForm.data.quantity || 0} *{" "}
                                        {activeProduct.total_price} = {totalPrice}
                                    </div>
                                </div>
                            </div>

                            <InputFile
                                labelWidth="140px"
                                label={t("Size/Attribute")}
                                name="attr"
                                error="attr"
                                errors={orderForm.errors}
                            >
                                <select
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value={orderForm.data.attr}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "attr",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">{t("Select Size/Attribute")}</option>
                                    {attrOptions.length > 0 ? (
                                        attrOptions.map((attr) => (
                                            <option key={attr} value={attr}>
                                                {attr}
                                            </option>
                                        ))
                                    ) : (
                                        <option value="N/A">{t("N/A")}</option>
                                    )}
                                </select>
                            </InputFile>

                            <Hr />

                            <InputFile
                                labelWidth="140px"
                                label={t("Area")}
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
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">{t("Select Area")}</option>
                                    <option value="Dhaka">{t("Inside Dhaka")}</option>
                                    <option value="Other">{t("Out side of Dhaka")}</option>
                                </select>
                            </InputFile>

                            <InputFile
                                labelWidth="140px"
                                label={t("Shipping Type")}
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
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">{t("Shipping Type")}</option>
                                    <option value="Courier">{t("Courier")}</option>
                                    <option value="Home">{t("Home Delivery")}</option>
                                    <option value="Hand">{t("Hand-To-Hand")}</option>
                                </select>
                            </InputFile>

                            <PrimaryButton type="submit" className="justify-center w-full sm:w-auto">
                                {t("Order")}
                            </PrimaryButton>
                        </form>
                    </div>
                ) : null}
            </Modal>
        </AppLayout>
    );
}
