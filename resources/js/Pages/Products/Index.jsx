import { Link, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import ApplicationName from "../../components/ApplicationName";
import Container from "../../components/dashboard/Container";
import ProductCard from "../../components/home/ProductCard";
import TextInput from "../../components/TextInput";
import CatLoop from "../../components/client/CatLoop";
import UserLayout from "../../Layouts/User/App";

function Heading() {
    return (
        <div>
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

function CategoriesPanel({ categories = [] }) {
    return (
        <div className="px-3 py-4">
            <div>
                <Link href={route("products.index")} className="inline-flex items-center px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    All Product
                </Link>
                <br />
            </div>
            {categories.map((item) => (
                <CatLoop key={item.id} item={item} style="font-bold" />
            ))}
        </div>
    );
}

export default function Index({
    products = [],
    categories = [],
    filters = {},
    loadMore = false,
}) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState(filters.search || "");

    const visitProducts = (nextSearch, nextSort, nextLimit = filters.limit) => {
        router.get(
            route("products.index"),
            {
                ...filters,
                search: nextSearch || undefined,
                sort: nextSort || "desc",
                limit: nextLimit,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            },
        );
    };

    const handleSort = (value) => {
        visitProducts(search.trim(), value);
    };

    const handleLoadMore = () => {
        visitProducts(
            search.trim(),
            filters.sort || "desc",
            Number(filters.limit || 20) + 20
        );
    };

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.search || "").trim();

        if (trimmedSearch === currentSearch) {
            return undefined;
        }

        const timeoutId = window.setTimeout(() => {
            visitProducts(trimmedSearch, filters.sort || "desc", 20);
        }, 400);

        return () => window.clearTimeout(timeoutId);
    }, [search, filters.search, filters.sort]);

    return (
        <UserLayout title="Products">
            <div className="py-4">
                <Heading />

                <Container>
                    <div className="items-start justify-start lg:flex">
                        <div
                            style={{ width: "300px" }}
                            className="hidden bg-white rounded-lg md:block"
                        >
                            <CategoriesPanel categories={categories} />
                        </div>

                        <div className="block p-2 mb-2 bg-white border rounded-md md:hidden">
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
                                <div className="mt-2 overflow-x-scroll border-t">
                                    <CategoriesPanel categories={categories} />
                                </div>
                            ) : null}
                        </div>

                        <div className="w-full px-2">
                            <div className="sticky z-20 flex flex-wrap items-center justify-between gap-3 p-3 mb-3 bg-white border rounded-md shadow-sm top-2">
                                <div>
                                    <TextInput
                                        type="search"
                                        placeholder="Search ...."
                                        className="py-1 mb-0"
                                        value={search}
                                        onChange={(e) =>
                                            setSearch(e.target.value)
                                        }
                                    />
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="relative">
                                        <select
                                            value={filters.sort || "desc"}
                                            onChange={(e) =>
                                                handleSort(e.target.value)
                                            }
                                            id="sort_by"
                                            className="w-32 py-2 pl-4 pr-10 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm appearance-none focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20"
                                        >
                                            <option value="desc">Newest</option>
                                            <option value="asc">Oldest</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div className="product_section">
                                {products.length ? (
                                    <div
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
                                ) : null}

                                {loadMore ? (
                                    <div className="text-center">
                                        <button
                                            onClick={handleLoadMore}
                                            className="px-6 py-2 mt-4 font-semibold text-white transition bg-green-600 rounded-md hover:bg-green-700"
                                            type="button"
                                        >
                                            Load More
                                        </button>
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    </div>
                </Container>
            </div>
        </UserLayout>
    );
}
