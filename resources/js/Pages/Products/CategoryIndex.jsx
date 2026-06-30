import { Link, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import ApplicationName from "../../components/ApplicationName";
import Container from "../../components/dashboard/Container";
import ProductCard from "../../components/home/ProductCard";
import CatLoop from "../../components/client/CatLoop";
import HeroSlider from "../../components/home/HeroSlider";
import TextInput from "../../components/TextInput";
import UserLayout from "../../Layouts/User/App";
import useTranslation from "../../hooks/useTranslation";

const PRIORITY_CATEGORY_SLUGS = [
    "womens-item",
    "mega-deals",
    "medicine",
    "grocery-item",
    "food-items",
];

const orderCategories = (categories = []) =>
    [...categories].sort((left, right) => {
        const leftPriority = PRIORITY_CATEGORY_SLUGS.indexOf(left.slug);
        const rightPriority = PRIORITY_CATEGORY_SLUGS.indexOf(right.slug);

        if (leftPriority === -1 && rightPriority === -1) return 0;
        if (leftPriority === -1) return 1;
        if (rightPriority === -1) return -1;

        return leftPriority - rightPriority;
    });

function Heading() {
    const { t } = useTranslation();

    return (
        <div>
            <div className="w-full mb-3 text-3xl text-center heading_center">
                <h2 className="flex justify-center gap-3">
                    <ApplicationName />
                    <span className="font-bold text-green-900">
                        {t("Marketplace")}
                    </span>
                </h2>
            </div>
        </div>
    );
}

function findCategoryBySlug(categories = [], slug = "") {
    for (const category of categories) {
        if (category.slug === slug) {
            return category;
        }

        const match = findCategoryBySlug(category.children ?? [], slug);
        if (match) {
            return match;
        }
    }

    return null;
}

function formatCategoryTitle(categories = [], slug = "") {
    const category = findCategoryBySlug(categories, slug);
    const title = category?.name || slug;

    return String(title)
        .replace(/[-_]+/g, " ")
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

export default function CategoryIndex({
    cat,
    products = [],
    categories = [],
    slides = [],
    filters = {},
    loadMore = false,
}) {
    const { t } = useTranslation();
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState(filters.search || "");
    const rows = products ?? [];
    const pageTitle = formatCategoryTitle(categories, cat);
    const orderedCategories = orderCategories(categories);

    const visitCategory = (
        nextSearch,
        nextSort,
        nextLimit = filters.limit || 20
    ) => {
        router.get(
            route("category.products", { cat }),
            {
                search: nextSearch || undefined,
                sort: nextSort || "desc",
                limit: nextLimit,
                country: filters.country || undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            },
        );
    };

    const handleSort = (value) => {
        visitCategory(search.trim(), value);
    };

    const handleLoadMore = () => {
        visitCategory(
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
            visitCategory(trimmedSearch, filters?.sort || "desc", 20);
        }, 400);

        return () => window.clearTimeout(timeoutId);
    }, [search, filters.search, filters.sort, cat]);

    return (
        <UserLayout title={pageTitle}>
            <HeroSlider slides={slides} />

            <Container>
                <Heading />

                <div className="product_section">
                    <div className="items-start justify-start sm:flex">
                        <div
                            style={{ width: "300px" }}
                            className="hidden bg-white rounded-lg md:block max-h-[calc(100vh-110px)] overflow-y-auto"
                        >
                            <div className="py-2">
                                <div className="px-2">
                                    <div>
                                        <Link
                                            href={route("products.index", { country: filters.country || undefined })}
                                            className="inline-flex items-center px-4 py-2 mb-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                        >
                                            {t("All Product")}
                                        </Link>
                                        <br />
                                    </div>
                                    {orderedCategories.map((item) => (
                                        <CatLoop
                                            key={item.id}
                                            item={item}
                                            active={cat === item.slug}
                                            cat={cat}
                                            style="font-bold"
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div className="block p-2 mb-2 bg-white border rounded-md md:hidden">
                            <div
                                onClick={() => setOpen((v) => !v)}
                                className="flex items-center justify-between cursor-pointer"
                            >
                                <div>{t("Categories")}</div>
                                <div>
                                    {open ? (
                                        <i className="fas fa-chevron-down"></i>
                                    ) : (
                                        <i className="fas fa-chevron-up"></i>
                                    )}
                                </div>
                            </div>
                            {open ? (
                                <div className="mt-2 overflow-x-auto border-t">
                                    <div className="my-3">
                                        <div className="w-full px-2 mx-auto space-y-3 max-w-8xl sm:px-4 lg:px-6">
                                            <div>
                                                <Link
                                                    href={route("products.index", { country: filters.country || undefined })}
                                                    className="inline-flex items-center px-4 py-2 mb-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                                >
                                                    {t("All Product")}
                                                </Link>
                                                <br />
                                            </div>
                                            {orderedCategories.map((item) => (
                                                <CatLoop
                                                    key={item.id}
                                                    item={item}
                                                    active={cat === item.slug}
                                                    cat={cat}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            ) : null}
                        </div>

                        <div className="w-full px-2">
                            <div className="sticky z-20 flex flex-wrap items-center gap-3 p-3 mb-3 bg-white border rounded-md shadow-sm top-2">
                                <div className="min-w-[220px]">
                                    <TextInput
                                        type="search"
                                        placeholder={t("Search ....")}
                                        className="w-full py-1 mb-0"
                                        value={search}
                                        onChange={(e) =>
                                            setSearch(e.target.value)
                                        }
                                    />
                                </div>
                                <div className="flex items-center gap-2 sm:ml-auto">
                                    <div className="relative">
                                        <select
                                            value={filters.sort || "desc"}
                                            onChange={(e) =>
                                                handleSort(e.target.value)
                                            }
                                            id="sort_by"
                                            className="w-32 py-2 pl-4 pr-10 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm appearance-none focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20"
                                        >
                                            <option value="desc">{t("Newest")}</option>
                                            <option value="asc">{t("Oldest")}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div className="grid w-full grid-cols-2 gap-2 sm:gap-3 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">
                                {rows.map((product) => (
                                    <ProductCard
                                        key={product.id}
                                        product={product}
                                    />
                                ))}
                            </div>

                            {!rows.length ? (
                                <div className="alert alert-info">
                                    {t("No Product Found !")}
                                </div>
                            ) : null}

                            {loadMore ? (
                                <div className="pt-4 text-center">
                                    <button
                                        type="button"
                                        onClick={handleLoadMore}
                                        className="px-6 py-2 font-semibold text-white transition bg-green-600 rounded-md hover:bg-green-700"
                                    >
                                        {t("Load More")}
                                    </button>
                                </div>
                            ) : null}
                        </div>
                    </div>
                </div>
            </Container>
        </UserLayout>
    );
}
