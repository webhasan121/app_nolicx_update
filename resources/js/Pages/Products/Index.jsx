import { Link, router } from "@inertiajs/react";
import { useState } from "react";
import ApplicationName from "../../components/ApplicationName";
import Container from "../../components/dashboard/Container";
import SecondaryButton from "../../components/SecondaryButton";
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
        <Container className="">
            <div>
                <Link href={route("products.index")} className="inline-flex items-center px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    All Product
                </Link>
                <br />
            </div>
            {categories.map((item) => (
                <CatLoop key={item.id} item={item} style="font-bold" />
            ))}
        </Container>
    );
}

export default function Index({
    products = [],
    categories = [],
    filters = {},
    loadMore = false,
}) {
    const [open, setOpen] = useState(false);

    const handleSort = (value) => {
        router.get(
            route("products.index"),
            {
                ...filters,
                sort: value,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    };

    const handleLoadMore = () => {
        router.get(
            route("products.index"),
            {
                ...filters,
                limit: Number(filters.limit || 50) + 1,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    };

    return (
        <UserLayout title="Products">
            <div className="py-4">
                <Heading />

                <Container>
                    <div className="items-start justify-start lg:flex">
                        <div
                            style={{ width: "300px" }}
                            className="hidden bg-white md:block"
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
                            <div className="flex flex-wrap items-center justify-between mb-3">
                                <div>
                                    <TextInput
                                        type="search"
                                        placeholder="Search ...."
                                        className="py-1"
                                        defaultValue={filters.search || ""}
                                    />
                                </div>
                                <div className="flex items-center justify-between space-x-2">
                                    <SecondaryButton>
                                        <i className="fas fa-filter"></i>
                                    </SecondaryButton>
                                    <select
                                        value={filters.sort || "desc"}
                                        onChange={(e) =>
                                            handleSort(e.target.value)
                                        }
                                        id="sort_by"
                                        className="w-24 py-1 rounded"
                                    >
                                        <option value="desc">Newest</option>
                                        <option value="asc">Oldest</option>
                                    </select>
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
                                            className="px-3 py-1 mt-3 border rounded"
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
