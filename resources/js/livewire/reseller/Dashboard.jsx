import { useMemo, useState } from "react";
import Modal from "../../components/Modal";
import DangerButton from "../../components/DangerButton";
import Hr from "../../components/Hr";
import NavLink from "../../components/NavLink";
import PrimaryButton from "../../components/PrimaryButton";
import ResponsiveNavLink from "../../components/ResponsiveNavLink";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import SectionSection from "../../components/dashboard/section/Section";
import VendorOrdersIndex from "../vendor/orders/Index";
import Container from "../../components/dashboard/Container";

function CategoryItem({ item, depth = 0 }) {
    if (!item || item.slug === "default-category") {
        return null;
    }

    return (
        <div
            className={`${depth === 0 ? "p-2 border-b border-gray-200 hover:bg-gray-50" : "py-1"} cursor-pointer`}
        >
            <NavLink
                href={route("reseller.resel-product.index", { cat: item.id })}
                className="p-0 text-sm border-b-0 text-inherit hover:text-inherit hover:border-transparent"
            >
                {item.name}
            </NavLink>

            {Array.isArray(item.children) && item.children.length > 0 ? (
                <div
                    className={`${depth === 0 ? "px-2 py-1 border-l" : "ps-2"}`}
                >
                    {item.children.map((child) => (
                        <CategoryItem
                            key={child.id}
                            item={child}
                            depth={depth + 1}
                        />
                    ))}
                </div>
            ) : null}
        </div>
    );
}

function categoryMatches(item, query) {
    return String(item?.name ?? "")
        .toLowerCase()
        .includes(query);
}

function filterCategories(items, query) {
    const normalizedQuery = query.trim().toLowerCase();

    if (!normalizedQuery) {
        return items ?? [];
    }

    return (items ?? [])
        .map((item) => {
            const children = filterCategories(
                item.children ?? [],
                normalizedQuery,
            );

            if (categoryMatches(item, normalizedQuery) || children.length > 0) {
                return {
                    ...item,
                    children,
                };
            }

            return null;
        })
        .filter(Boolean);
}

function OverviewDiv({ title, children }) {
    return (
        <div
            className="relative p-3 overflow-hidden rounded shadow d-block"
            style={{ backgroundColor: "orange", zIndex: 1, color: "white" }}
        >
            <style
                dangerouslySetInnerHTML={{
                    __html: `
                        .div_wrapper {
                            position: absolute;
                            bottom: -100px;
                            right: -100px;
                            width: 200px;
                            height: 200px;
                            border-radius: 50%;
                            background: radial-gradient(rgb(12, 165, 94), transparent);
                            z-index: -1;
                        }

                        .div_wrapper::after {
                            content: "";
                            position: absolute;
                            width: 80px;
                            height: 80px;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            border-radius: 50%;
                            background: radial-gradient(green, transparent);
                        }
                    `,
                }}
            />

            <div className="mb-3 text-md">{title}</div>
            <div className="text-2xl text-end">{children}</div>
            <div className="div_wrapper"></div>
        </div>
    );
}

const money = (value) => `Tk ${Number(value ?? 0).toLocaleString()}`;

function ProductCard({ product }) {
    const salePrice =
        product?.offer_type && product?.discount
            ? product.discount
            : product.price;

    return (
        <div className="relative overflow-hidden bg-white rounded shadow">
            {product?.offer_type ? (
                <div className="bg-orange-500 discount-badge ">
                    {product?.price
                        ? Math.round(
                              (((product.price - product.discount) /
                                  product.price) *
                                  100 +
                                  Number.EPSILON) *
                                  10,
                          ) / 10
                        : 0}
                    %
                </div>
            ) : null}

            <div className="p-1 overflow-hidden shadow-md">
                <img
                    style={{ height: 120 }}
                    src={`/storage/${product?.thumbnail}`}
                    className="object-cover w-full"
                    alt="image"
                />
            </div>

            <div className="flex flex-col justify-between p-2 bg-white h-34">
                <NavLink
                    href={route("reseller.resel-product.veiw", {
                        pd: product?.id,
                    })}
                    className="p-0 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                >
                    <div className="text-sm text-start product-title-clamp-3">
                        {product?.name ?? "N/A"}
                    </div>
                </NavLink>

                <div>
                    <div className="mb-3 text-md">
                        {product?.offer_type ? (
                            <>
                                <div className="bold">
                                    {salePrice ?? "0"} TK
                                </div>
                                <div className="text-xs">
                                    <del>{product?.price ?? "0"} TK</del>
                                </div>
                            </>
                        ) : (
                            <div className="bold">
                                {product?.price ?? "0"} TK
                            </div>
                        )}
                    </div>

                    <div className="flex items-center justify-center text-sm">
                        <Hr />
                        <NavLink
                            href={route("reseller.resel-product.veiw", {
                                pd: product?.id,
                            })}
                            className="w-full p-0 border-b-0 hover:border-transparent"
                        >
                            <PrimaryButton
                                type="button"
                                className="flex justify-between w-full text-center "
                            >
                                Purchase <i className="pl-2 fas fa-angle-right"></i>
                            </PrimaryButton>
                        </NavLink>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({
    tp,
    vendor,
    dashboardOverview = {},
    category,
    categories = [],
    products = [],
    vendorOrdersIndex,
    activeNav,
}) {
    const [open, setOpen] = useState(false);
    const [categorySearch, setCategorySearch] = useState("");
    const filteredCategories = useMemo(
        () => filterCategories(categories, categorySearch),
        [categories, categorySearch],
    );

    const closeCategoryModal = () => {
        setOpen(false);
        setCategorySearch("");
    };

    return (
            <Container>
                <div>
                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                        <OverviewDiv title="Product">{tp}</OverviewDiv>

                        <OverviewDiv title="Vendor Shops">
                            {vendor}
                        </OverviewDiv>
                        <OverviewDiv title="Today sell">
                            {money(dashboardOverview?.today_sell)}
                        </OverviewDiv>
                        <OverviewDiv title="Monthly sell">
                            {money(dashboardOverview?.monthly_sell)}
                        </OverviewDiv>
                        <OverviewDiv title="Product stock">
                            {dashboardOverview?.product_stock ?? "0"}
                        </OverviewDiv>
                        <OverviewDiv title="Total product stock price">
                            {money(dashboardOverview?.total_product_stock_price)}
                        </OverviewDiv>
                        <OverviewDiv title="Yearly sell amount">
                            {money(dashboardOverview?.yearly_sell_amount)}
                        </OverviewDiv>
                        <OverviewDiv title="Total amount">
                            {money(dashboardOverview?.total_amount)}
                        </OverviewDiv>
                        <OverviewDiv title="Monthly profit">
                            {money(dashboardOverview?.monthly_profit)}
                        </OverviewDiv>
                        <OverviewDiv title="Daily profit">
                            {money(dashboardOverview?.daily_profit)}
                        </OverviewDiv>
                    </div>
                    <Hr />

                    <SectionSection>
                        <SectionHeader
                            title="Chose From Different Category"
                            content={`We have ${category} categories, chose as you need from our different category.`}
                        />
                        <SectionInner>
                            <PrimaryButton
                                type="button"
                                onClick={() => setOpen(true)}
                            >
                                categories
                            </PrimaryButton>
                        </SectionInner>
                    </SectionSection>

                    <VendorOrdersIndex
                        orderIndex={vendorOrdersIndex}
                        activeNav={activeNav}
                        embedded
                    />

                    <Hr />
                    <SectionInner>
                        <p className="mb-2 text-xs">Resel Products from vendor</p>
                        <div
                            style={{
                                display: "grid",
                                justifyContent: "start",
                                gridTemplateColumns: "repeat(auto-fill, 170px)",
                                gridGap: 10,
                            }}
                        >
                            {products.length > 0
                                ? products.map((product) => (
                                      <ProductCard
                                          key={product.id}
                                          product={product}
                                      />
                                  ))
                                : null}
                        </div>
                    </SectionInner>
                    <ResponsiveNavLink
                        href={route("reseller.resel-product.index")}
                        active={route().current("reseller.resel-product.*")}
                    >
                        <i className="w-6 pr-2 fas fa-sync"></i> View All
                    </ResponsiveNavLink>
                </div>
                <Modal show={open} onClose={closeCategoryModal}>
                    <div className="flex flex-col gap-3 p-3 border-b sm:flex-row sm:items-center sm:justify-between">
                        <div>Explore Category</div>
                        <input
                            type="search"
                            value={categorySearch}
                            onChange={(event) =>
                                setCategorySearch(event.target.value)
                            }
                            placeholder="Search category"
                            className="w-full text-sm border-gray-300 rounded sm:w-56 focus:border-orange-500 focus:ring-orange-500"
                            autoComplete="off"
                        />
                    </div>
                    <div className="p-3 text-sm text-gray-600">
                        <div className="mb-2">
                            <NavLink
                                href={route("reseller.resel-product.index")}
                                className="p-0 text-sm border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                            >
                                View All Products
                            </NavLink>
                        </div>
                        {filteredCategories.length > 0 ? (
                            filteredCategories.map((item) => (
                                <CategoryItem key={item.id} item={item} />
                            ))
                        ) : (
                            <div className="py-6 text-center text-gray-500">
                                No category found.
                            </div>
                        )}
                    </div>
                    <hr className="my-1" />
                    <div className="flex items-center justify-end p-3">
                        <DangerButton onClick={closeCategoryModal}>
                            close
                        </DangerButton>
                    </div>
                </Modal>
            </Container>
    );
}
