import { Link } from "@inertiajs/react";
import { useState } from "react";
import ApplicationName from "../../components/ApplicationName";
import Container from "../../components/dashboard/Container";
import ProductCard from "../../components/home/ProductCard";
import CatLoop from "../../components/client/CatLoop";
import HeroSlider from "../../components/home/HeroSlider";
import UserLayout from "../../Layouts/User/App";

function Heading() {
    return (
        <div className="container">
            <div className="w-full mb-3 text-3xl text-center heading_center">
                <h2 className="flex justify-center gap-3">
                    <ApplicationName />
                    <span className="font-bold text-green-900">
                        Marketplace
                    </span>
                </h2>
            </div>
        </div>
    );
}

export default function CategoryIndex({
    cat,
    products = [],
    categories = [],
    slides = [],
}) {
    const [open, setOpen] = useState(false);

    return (
        <UserLayout title={cat}>
            <HeroSlider slides={slides} />

            <Container>
                <div className="container">
                    <Heading />

                    <div className="flex items-center justify-start py-3 mb-3 border-y">
                        <i className="fas fa-home pe-2"></i>
                        <div>Category</div>
                        <Link
                            href={route("category.products", { cat })}
                            className=""
                        >
                            <span className="px-2 text-gray-600 text-primary">
                                {cat}
                            </span>
                        </Link>
                    </div>

                    <div className="product_section">
                        <div className="w-full md:flex">
                            <div
                                style={{ width: "300px" }}
                                className="hidden p-3 bg-white md:block"
                            >
                                <div>
                                    <Link
                                        href={route("products.index")}
                                        className="inline-flex items-center px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                    >
                                        All Product
                                    </Link>
                                    <br />
                                </div>
                                {categories.map((item) => (
                                    <CatLoop
                                        key={item.id}
                                        item={item}
                                        active={cat === item.slug}
                                        cat={cat}
                                        style="font-bold"
                                    />
                                ))}
                            </div>

                            <div className="block px-3 mb-2 bg-white md:hidden">
                                <div
                                    onClick={() => setOpen((v) => !v)}
                                    className="flex items-center justify-between cursor-pointer"
                                >
                                    <div>Categories</div>
                                    <div>
                                        {open ? (
                                            <i className="fas fa-chevron-down"></i>
                                        ) : (
                                            <i className="fas fa-chevron-up"></i>
                                        )}
                                    </div>
                                </div>
                                {open ? (
                                    <div className="mt-2 border-t">
                                        <div>
                                            <Link
                                                href={route("products.index")}
                                                className="inline-flex items-center px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                            >
                                                All Product
                                            </Link>
                                            <br />
                                        </div>
                                        <div>
                                            {categories.map((item) => (
                                                <CatLoop
                                                    key={item.id}
                                                    item={item}
                                                    active={cat === item.slug}
                                                    cat={cat}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                ) : null}
                            </div>

                            <div className="w-full px-2">
                                <div
                                    className="w-full"
                                    style={{
                                        display: "grid",
                                        justifyContent: "start",
                                        gridTemplateColumns:
                                            "repeat(auto-fill, minmax(160px, 1fr))",
                                        gridGap: "10px",
                                    }}
                                >
                                    {products.map((product) => (
                                        <ProductCard
                                            key={product.id}
                                            product={product}
                                        />
                                    ))}
                                </div>

                                {!products.length ? (
                                    <div className="alert alert-info">
                                        No Product Found !
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </UserLayout>
    );
}
