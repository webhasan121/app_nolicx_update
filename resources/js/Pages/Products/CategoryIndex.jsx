import { Link, router } from "@inertiajs/react";
import { useMemo, useState } from "react";
import CommonHeading from "../../components/client/CommonHeading";
import Container from "../../components/dashboard/Container";
import ProductCard from "../../components/home/ProductCard";
import CatLoop from "../../components/client/CatLoop";
import HeroSlider from "../../components/home/HeroSlider";
import UserLayout from "../../Layouts/User/App";

export default function CategoryIndex({
    cat,
    products = [],
    categories = [],
    slides = [],
    filters = {},
}) {
    const [open, setOpen] = useState(false);
    const rows = products?.data ?? [];

    const pagination = useMemo(() => {
        const links = products?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [products?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url, window.location.origin);

        router.get(
            route("category.products", { cat }),
            {
                page: nextUrl.searchParams.get("page") ?? filters?.page ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            }
        );
    };

    return (
        <UserLayout title={cat}>
            <HeroSlider slides={slides} />

            <Container>
                <div className="container">
                    <CommonHeading />

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
                                    {rows.map((product) => (
                                        <ProductCard
                                            key={product.id}
                                            product={product}
                                        />
                                    ))}
                                </div>

                                {!rows.length ? (
                                    <div className="alert alert-info">
                                        No Product Found !
                                    </div>
                                ) : null}

                                {pagination.pages.length ? (
                                    <div className="w-full pt-4">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="px-3 py-1 bg-white border rounded disabled:opacity-50"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                Previous
                                            </button>

                                            {pagination.pages.map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`px-3 py-1 border rounded ${
                                                        link.active
                                                            ? "bg-gray-900 text-white"
                                                            : "bg-white"
                                                    }`}
                                                    onClick={() => goToPage(link.url)}
                                                >
                                                    {link.label}
                                                </button>
                                            ))}

                                            <button
                                                type="button"
                                                disabled={!pagination.next?.url}
                                                className="px-3 py-1 bg-white border rounded disabled:opacity-50"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
                                            </button>
                                        </div>
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
