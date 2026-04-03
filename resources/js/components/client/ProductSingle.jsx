import { Link } from "@inertiajs/react";
import { useState } from "react";
import axios from "axios";
import SecondaryButton from "../SecondaryButton";
import Hr from "../Hr";
import ProductsLoop from "./ProductsLoop";

export default function ProductSingle({ product, relatedProduct = [] }) {
    const [copied, setCopied] = useState(false);

    if (!product) return null;

    const addToCart = async () => {
        try {
            await axios.post("/cart/add", { product_id: product.id });
            window.dispatchEvent(new Event("cart-updated"));
        } catch (error) {
            // keep silent here, UserDash flash/toast handles server messages
        }
    };

    const copyLink = async () => {
        const url = route("products.details", { id: product.id, slug: product.slug });
        await navigator.clipboard.writeText(url);
        setCopied(true);
        setTimeout(() => setCopied(false), 1200);
    };

    const attrValues = product?.attr?.value
        ? String(product.attr.value).split(",").map((v) => v.trim()).filter(Boolean)
        : [];

    return (
        <div>
            <div className="lg:flex justify-start item-start p-2 relative">
                <div className="p-3 w-full" style={{ maxWidth: "600px" }}>
                    <img
                        className="w-full p-2 border rounded-md"
                        style={{ objectFit: "contain", maxWidth: "600px" }}
                        src={`/storage/${product.thumbnail}`}
                        alt={product.title}
                    />

                    <div className="mt-4">
                        <h3 className="text-lg font-bold">{product.title}</h3>
                        <div className="text-sm">
                            <Link
                                href={route("category.products", { cat: product?.category?.slug })}
                                className="text_primary bold"
                            >
                                {product?.category?.name ?? "Undefined"}
                            </Link>
                        </div>
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

                    <div className="py-3">
                        {product.offer_type ? (
                            <div>
                                <div className="text-xl">
                                    Price : <strong className="text_secondary">{product.discount} TK</strong>
                                </div>
                                <del className="text-sm">MRP: {product.price} TK</del>
                            </div>
                        ) : (
                            <div className="text-[22px] font-bold text_primary">
                                Price : {product.price} TK
                            </div>
                        )}
                    </div>

                    <Hr />
                    <div className="purchase-info flex justify-start items-center w-full space-x-2">
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
                        <button
                            type="button"
                            className="flex items-center justify-center w-36 px-4 py-2 text-white transition rounded"
                            style={{ background: "#4f4f4f" }}
                            onClick={copyLink}
                        >
                            <i className="mr-2 fas fa-link"></i>
                            {copied ? "Copied" : "Copy Link"}
                        </button>
                    </div>
                </div>
            </div>

            {relatedProduct?.length > 0 && (
                <>
                    <hr />
                    <div className="sm:w-full p-3">
                        <div className="font-bold">Related Products</div>
                        <br />
                        <div
                            className="product_section"
                            style={{
                                display: "grid",
                                justifyContent: "start",
                                gridTemplateColumns: "repeat(auto-fill, 160px)",
                                gridGap: "10px",
                            }}
                        >
                            <ProductsLoop products={relatedProduct} />
                        </div>
                    </div>
                </>
            )}
        </div>
    );
}
