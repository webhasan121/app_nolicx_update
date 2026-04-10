import { Head, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";

function CategoryTree({ categories = [], activeCat, search = "" }) {
    return categories
        .filter((item) => item.slug !== "default-category")
        .map((item) => (
            <div
                key={item.id}
                className="p-2 border-b border-gray-200 hover:bg-gray-50 cursor-pointer"
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
                            <span className="text-sm text-gray-500">
                                No subcategories
                            </span>
                        )}
                    </div>
                </div>
            </div>
        ));
}

function ProductCard({ product, onPurchase }) {
    const discountPercent = useMemo(() => {
        if (!product.offer_type || !product.price) return 0;
        const diff = product.price - (product.discount ?? 0);
        return Math.round(((diff / product.price) * 100) * 10) / 10;
    }, [product]);

    return (
        <div className="bg-white rounded shadow overflow-hidden relative">
            {product.offer_type ? (
                <div className="discount-badge bg-orange-600">
                    {discountPercent}%</div>
            ) : null}

            <div className="overflow-hidden shadow-md p-1">
                {product.thumbnail_url ? (
                    <img
                        style={{ height: "120px" }}
                        src={product.thumbnail_url}
                        className="w-full object-cover"
                        alt="image"
                    />
                ) : null}
            </div>

            <div className="p-2 bg-white h-34 flex flex-col justify-between">
                <NavLink
                    href={route("reseller.resel-product.veiw", {
                        pd: product.id,
                    })}
                >
                    <div className="text-sm">{product.name ?? "N/A"}</div>
                </NavLink>

                <div>
                    <div className="text-md mb-3">
                        {product.offer_type ? (
                            <>
                                <div className="bold">
                                    {product.discount ?? "0"} TK
                                </div>
                                <div className="text-xs">
                                    <del>{product.price ?? "0"} TK</del>
                                </div>
                            </>
                        ) : (
                            <div className="bold">
                                {product.price ?? "0"} TK
                            </div>
                        )}
                    </div>

                    <div className="flex justify-center items-center text-sm">
                        <Hr />
                        <PrimaryButton
                            type="button"
                            className="text-center w-full flex justify-between"
                            onClick={() => onPurchase(product)}
                        >
                            Purchase{" "}
                            <i className="fas fa-angle-right pl-2"></i>
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
    const { auth } = usePage().props;
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const [showOrderModal, setShowOrderModal] = useState(false);
    const [activeProduct, setActiveProduct] = useState(null);
    const [search, setSearch] = useState(filters.search ?? "");

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
            title="Resel Products"
            header={
                <PageHeader>
                    <div className="flex justify-between">
                        <div>
                            Resel Products
                            <br />
                            <div>
                                <NavLink
                                    href={route("reseller.resel-product.index")}
                                    active={route().current(
                                        "reseller.resel-product.*"
                                    )}
                                >
                                    Product
                                </NavLink>
                            </div>
                        </div>

                        <div>
                            <div className="flex bg-indigo-900 border border-indigo-900 rounded-xl">
                                <div
                                    className="px-2 bg-white"
                                    title="Total Resell Products"
                                >
                                    {totalReselProducts}
                                </div>
                                <div
                                    className="px-2 text-white"
                                    title="Max Resell Products"
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
                    <div className="p-2 bg-red-200 text-red-800">
                        You have reached the maximum number of products you can
                        upload {shop?.max_resell_product ?? 0}. Please delete
                        some products to add new ones or upgrade your plan.
                    </div>
                ) : null}

                <div>
                    <div>
                        <div className="flex flex-wrap items-center justify-between gap-2 py-2">
                            <button
                                type="button"
                                onClick={() => setShowCategoryModal(true)}
                                className="flex items-center justify-between gap-3 rounded-md border px-3 py-2 text-sm hover:bg-white"
                            >
                                <span>Categories</span>
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
                                className="py-1"
                                placeholder="Search products..."
                            />
                        </div>

                        <div
                            style={{
                                display: "grid",
                                justifyContent: "start",
                                gridTemplateColumns:
                                    "repeat(auto-fill, 160px)",
                                gridGap: "10px",
                            }}
                        >
                            {(products?.data ?? []).map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    onPurchase={openOrderModal}
                                />
                            ))}
                        </div>
                    </div>

                    {(products?.data ?? []).length < 1 ? (
                        <div className="p-2 bg-gray-200 h-auto">
                            No Products Found !
                        </div>
                    ) : null}

                    {pagination.pages.length ? (
                        <div className="w-full pt-4">
                            <div className="flex w-full items-center justify-between gap-3">
                                <div className="text-sm text-slate-700">
                                    {resultSummary}
                                </div>
                                <div className="flex items-center md:justify-end">
                                    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                        <button
                                            type="button"
                                            disabled={!pagination.prev?.url}
                                            className="border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                            onClick={() => goToPage(pagination.prev?.url)}
                                        >
                                            Previous
                                        </button>
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
                                            className="px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                            onClick={() => goToPage(pagination.next?.url)}
                                        >
                                            Next
                                        </button>
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
                        <div className="p-3 flex items-center justify-between">
                            <div>Explore Categories</div>
                            <button
                                type="button"
                                onClick={() => setShowCategoryModal(false)}
                            >
                                <i className="fas fa-times"></i>
                            </button>
                        </div>
                        <div className="px-3 pb-3">
                            <NavLink
                                href={route("reseller.resel-product.index")}
                            >
                                View All Products
                            </NavLink>
                        </div>
                        <hr />
                        <div className="p-3 flex-1 overflow-y-scroll">
                            <CategoryTree
                                categories={categories}
                                activeCat={filters?.cat}
                                search={search.trim()}
                    />
                </div>
                <hr />
                <div className="w-full text-end p-3">
                    <SecondaryButton
                        type="button"
                        onClick={() => setShowCategoryModal(false)}
                    >
                        <i className="fas fa-times mr-2"></i> Close
                    </SecondaryButton>
                </div>
            </Modal>

            <Modal
                show={showOrderModal}
                onClose={closeOrderModal}
                maxWidth="md"
            >
                {activeProduct ? (
                    <div>
                        <div className="p-3 bold border-b flex justify-between items-center">
                            <div>Purchase</div>
                            <div className="bold text-lg">
                                {activeProduct.total_price} TK
                            </div>
                        </div>
                        <div className="flex items-start justify-start mb-3 p-5 bg-gray-100">
                            <div className="flex">
                                {activeProduct.thumbnail_url ? (
                                    <img
                                        src={activeProduct.thumbnail_url}
                                        className="w-12 h-12 rounded shadow mr-3"
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
                                            <div className="bold">
                                                Price :{" "}
                                                {activeProduct.total_price} TK
                                            </div>
                                            <div className="text-xs">
                                                <del>
                                                    MRP:{" "}
                                                    {activeProduct.price ?? "0"}{" "}
                                                    TK
                                                </del>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="bold">
                                            Price :{" "}
                                            {activeProduct.price ?? "0"} TK
                                        </div>
                                    )}
                                    <div className="text-xs">
                                        Available Stock:{" "}
                                        {activeProduct.unit ?? "0"}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form onSubmit={submitOrder} className="p-5">
                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="Name"
                                value={orderForm.data.name}
                                onChange={(e) =>
                                    orderForm.setData("name", e.target.value)
                                }
                            />
                            {orderForm.errors.name ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.name}
                                </div>
                            ) : null}

                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="Phone"
                                value={orderForm.data.phone}
                                onChange={(e) =>
                                    orderForm.setData("phone", e.target.value)
                                }
                            />
                            {orderForm.errors.phone ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.phone}
                                </div>
                            ) : null}

                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="District"
                                value={orderForm.data.district}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "district",
                                        e.target.value
                                    )
                                }
                            />
                            {orderForm.errors.district ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.district}
                                </div>
                            ) : null}
                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="Upozila"
                                value={orderForm.data.upozila}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "upozila",
                                        e.target.value
                                    )
                                }
                            />
                            {orderForm.errors.upozila ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.upozila}
                                </div>
                            ) : null}
                            <textarea
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="Full Address"
                                value={orderForm.data.location}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "location",
                                        e.target.value
                                    )
                                }
                            ></textarea>
                            {orderForm.errors.location ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.location}
                                </div>
                            ) : null}
                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="Road No"
                                value={orderForm.data.road_no}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "road_no",
                                        e.target.value
                                    )
                                }
                            />
                            <input
                                className="w-full rounded-md p-2 mb-2"
                                placeholder="House No"
                                value={orderForm.data.house_no}
                                onChange={(e) =>
                                    orderForm.setData(
                                        "house_no",
                                        e.target.value
                                    )
                                }
                            />

                            <div className="mb-2">
                                <select
                                    className="rounded-md py-1 border w-full"
                                    value={orderForm.data.quantity}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "quantity",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">
                                        Select Quantity
                                    </option>
                                    {quantityOptions.map((qty) => (
                                        <option key={qty} value={qty}>
                                            {qty}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {orderForm.errors.quantity ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.quantity}
                                </div>
                            ) : null}

                            <div className="p-2 bg-indigo-100 mb-2">
                                <div className="text-xs">
                                    {Number(activeProduct.unit) < 1
                                        ? "Stock Out"
                                        : `You can order maximum ${activeProduct.unit} item`}
                                </div>
                                <div className="flex justify-between items-center">
                                    <div>Total</div>
                                    <div>
                                        {orderForm.data.quantity || 0} *{" "}
                                        {activeProduct.total_price} = {totalPrice}
                                    </div>
                                </div>
                            </div>

                            <div className="mb-2">
                                <select
                                    className="rounded-md py-1 border w-full"
                                    value={orderForm.data.attr}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "attr",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">
                                        Select Size/Attribute
                                    </option>
                                    {attrOptions.length > 0 ? (
                                        attrOptions.map((attr) => (
                                            <option key={attr} value={attr}>
                                                {attr}
                                            </option>
                                        ))
                                    ) : (
                                        <option value="N/A">N/A</option>
                                    )}
                                </select>
                            </div>
                            {orderForm.errors.attr ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.attr}
                                </div>
                            ) : null}

                            <Hr />

                            <div className="mb-2">
                                <select
                                    className="rounded py-1 w-full"
                                    value={orderForm.data.area_condition}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "area_condition",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">Select Area</option>
                                    <option value="Dhaka">Inside Dhaka</option>
                                    <option value="Other">
                                        Out side of Dhaka
                                    </option>
                                </select>
                            </div>
                            {orderForm.errors.area_condition ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.area_condition}
                                </div>
                            ) : null}

                            <div className="mb-2">
                                <select
                                    className="rounded py-1 w-full"
                                    value={orderForm.data.delevery}
                                    onChange={(e) =>
                                        orderForm.setData(
                                            "delevery",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="">Shipping Type</option>
                                    <option value="Courier">Courier</option>
                                    <option value="Home">Home Delivery</option>
                                    <option value="Hand">Hand-To-Hand</option>
                                </select>
                            </div>
                            {orderForm.errors.delevery ? (
                                <div className="text-red-900 text-xs">
                                    {orderForm.errors.delevery}
                                </div>
                            ) : null}

                            <PrimaryButton type="submit">
                                Order
                            </PrimaryButton>
                        </form>
                    </div>
                ) : null}
            </Modal>
        </AppLayout>
    );
}
