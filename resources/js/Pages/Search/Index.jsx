import { router } from "@inertiajs/react";
import { useMemo } from "react";
import DisplayCategory from "../../components/client/DisplayCategory";
import ProductCart from "../../components/client/ProductCart";
import ShopsCart from "../../components/client/ShopsCart";
import Hr from "../../components/Hr";
import Container from "../../components/dashboard/Container";
import UserLayout from "../../Layouts/User/App";

export default function Index({ q = "", product = {}, shop = [], category = [] }) {
    const rows = product?.data ?? [];

    const pagination = useMemo(() => {
        const links = product?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [product?.links]);

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url, window.location.origin);

        router.get(
            route("search"),
            {
                q,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            }
        );
    };

    return (
        <UserLayout title="Search">
            <Container>
                <div className="flex items-center justify-start py-3 mb-3">
                    <i className="fas fa-home pe-2"></i>
                    <div>search</div>
                    <div className="px-2">{q}</div>
                </div>

                {rows.length > 0 ? (
                    <>
                        <div className="product_section">
                            <div
                                style={{
                                    display: "grid",
                                    justifyContent: "start",
                                    gridTemplateColumns: "repeat(auto-fill, minmax(160px, 1fr))",
                                    gridGap: "10px",
                                }}
                            >
                                {rows.map((prod) => (
                                    <ProductCart key={prod.id} product={prod} />
                                ))}
                            </div>
                        </div>

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
                                                link.active ? "bg-gray-900 text-white" : "bg-white"
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
                    </>
                ) : (
                    <p>No products found.</p>
                )}
            </Container>

            <Hr />

            <Container>
                {shop?.length > 0 ? (
                    <>
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns: "repeat(auto-fit, 300px)",
                                justifyContent: "start",
                                alignItems: "start",
                                gridGap: "10px",
                            }}
                        >
                            {shop.map((sh) => (
                                <ShopsCart key={sh.id} shop={sh} />
                            ))}
                        </div>
                        <Hr />
                    </>
                ) : null}
            </Container>

            <Container>
                {category?.length > 0 ? (
                    <div>
                        <div>Categories</div>
                        <DisplayCategory categories={category} />
                    </div>
                ) : null}
            </Container>
        </UserLayout>
    );
}
