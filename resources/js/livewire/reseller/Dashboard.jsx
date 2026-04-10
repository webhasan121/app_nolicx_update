import { useState } from "react";
import Modal from "../../components/Modal";
import DangerButton from "../../components/DangerButton";
import Hr from "../../components/Hr";
import PrimaryButton from "../../components/PrimaryButton";
import ResponsiveNavLink from "../../components/ResponsiveNavLink";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import SectionSection from "../../components/dashboard/section/Section";
import VendorOrdersIndex from "../vendor/orders/Index";

function CategoryItem({ item, depth = 0 }) {
    if (!item || item.slug === "default-category") {
        return null;
    }

    return (
        <div className={`${depth === 0 ? "p-2 border-b border-gray-200 hover:bg-gray-50" : "py-1"} cursor-pointer`}>
            <a
                href={route("reseller.resel-product.index", { cat: item.id })}
                className="text-sm"
            >
                {item.name}
            </a>

            {Array.isArray(item.children) && item.children.length > 0 ? (
                <div className={`${depth === 0 ? "px-2 py-1 border-l" : "ps-2"}`}>
                    {item.children.map((child) => (
                        <CategoryItem key={child.id} item={child} depth={depth + 1} />
                    ))}
                </div>
            ) : null}
        </div>
    );
}

function OverviewDiv({ title, children }) {
    return (
        <div
            className="rounded d-block shadow p-3 relative overflow-hidden"
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

            <div className="text-md mb-3">{title}</div>
            <div className="text-end text-2xl">{children}</div>
            <div className="div_wrapper"></div>
        </div>
    );
}

function ProductCard({ product }) {
    const salePrice =
        product?.offer_type && product?.discount ? product.discount : product.price;

    return (
        <div className="bg-white rounded shadow overflow-hidden relative">
            {product?.offer_type ? (
                <div className="discount-badge bg-orange-600 ">
                    {product?.price
                        ? Math.round(
                              (((product.price - product.discount) / product.price) *
                                  100 +
                                  Number.EPSILON) *
                                  10
                          ) / 10
                        : 0}
                    %
                </div>
            ) : null}

            <div className="overflow-hidden shadow-md p-1">
                <img
                    style={{ height: 120 }}
                    src={`/storage/${product?.thumbnail}`}
                    className="w-full object-cover"
                    alt="image"
                />
            </div>

            <div className="p-2 bg-white h-34 flex flex-col justify-between">
                <a href={route("reseller.resel-product.veiw", { pd: product?.id })}>
                    <div className="text-sm">{product?.name ?? "N/A"}</div>
                </a>

                <div>
                    <div className="text-md mb-3">
                        {product?.offer_type ? (
                            <>
                                <div className="bold">{salePrice ?? "0"} TK</div>
                                <div className="text-xs">
                                    <del>{product?.price ?? "0"} TK</del>
                                </div>
                            </>
                        ) : (
                            <div className="bold">{product?.price ?? "0"} TK</div>
                        )}
                    </div>

                    <div className="flex justify-center items-center text-sm">
                        <Hr />
                        <PrimaryButton
                            type="button"
                            className=" text-center w-full flex justify-between "
                        >
                            Purchase <i className="fas fa-angle-right pl-2"></i>
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({
    tp,
    vendor,
    category,
    categories = [],
    products = [],
    vendorOrdersIndex,
    activeNav,
}) {
    const [open, setOpen] = useState(false);

    return (
        <div>
            <div>
                <div>
                    <div>
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns:
                                    "repeat(auto-fill, minmax(150px, 1fr))",
                                gridGap: 20,
                            }}
                        >
                            <OverviewDiv title="Product">{tp}</OverviewDiv>

                            <OverviewDiv title="Vendor Shops">
                                {vendor}
                            </OverviewDiv>
                        </div>
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

                    <SectionSection>
                        <VendorOrdersIndex
                            orderIndex={vendorOrdersIndex}
                            activeNav={activeNav}
                        />
                    </SectionSection>

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
                        <i className="fas fa-sync pr-2 w-6"></i> View All
                    </ResponsiveNavLink>
                </div>

                <Modal show={open} onClose={() => setOpen(false)}>
                    <div className="p-3 border-b">Explore Category</div>
                    <div className="p-3 text-sm text-gray-600">
                        <div className="mb-2">
                            <a
                                href={route("reseller.resel-product.index")}
                                className="text-sm"
                            >
                                View All Products
                            </a>
                        </div>
                        {(categories ?? []).map((item) => (
                            <CategoryItem key={item.id} item={item} />
                        ))}
                    </div>
                    <hr className="my-1" />
                    <div className="flex justify-end items-center p-3">
                        <DangerButton onClick={() => setOpen(false)}>
                            close
                        </DangerButton>
                    </div>
                </Modal>
            </div>
        </div>
    );
}
