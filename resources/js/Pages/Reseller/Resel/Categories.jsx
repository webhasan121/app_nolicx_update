import { Head } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import NavLink from "../../../components/NavLink";
import PageHeader from "../../../components/dashboard/PageHeader";

function ucfirst(value) {
    const text = String(value ?? "");

    return text.charAt(0).toUpperCase() + text.slice(1);
}

export default function Categories({ categories = [], cat }) {
    return (
        <AppLayout
            title="Categories to resel"
            header={
                <PageHeader>
                    Categories to resel
                    <br />
                    <div>
                        <NavLink href={route("reseller.resel-product.index")}>
                            View All Products
                        </NavLink>
                    </div>
                </PageHeader>
            }
        >
            <Head title="Categories to resel" />

            <div>
                {categories.map((item) =>
                    item.slug !== "default-category" ? (
                        <div
                            key={item.id}
                            className="p-2 border-b border-gray-200 hover:bg-gray-50 cursor-pointer "
                        >
                            <div>
                                <NavLink
                                    active={String(cat ?? "") === String(item.id)}
                                    href={route("reseller.resel-product.index", {
                                        cat: item.id,
                                    })}
                                >
                                    {ucfirst(item.name)}
                                </NavLink>

                                <div>
                                    {(item.children ?? []).length > 0 ? (
                                        <div className="px-2 py-1 border-l ">
                                            {item.children.map((child) => (
                                                <div key={child.id} className="">
                                                    <NavLink
                                                        active={
                                                            String(cat ?? "") ===
                                                            String(child.id)
                                                        }
                                                        href={route(
                                                            "reseller.resel-product.index",
                                                            { cat: child.id }
                                                        )}
                                                    >
                                                        {ucfirst(child.name)}
                                                    </NavLink>

                                                    <div className="ps-2">
                                                        {(child.children ?? []).map(
                                                            (sc) => (
                                                                <NavLink
                                                                    key={sc.id}
                                                                    active={
                                                                        String(
                                                                            cat ?? ""
                                                                        ) ===
                                                                        String(sc.id)
                                                                    }
                                                                    href={route(
                                                                        "reseller.resel-product.index",
                                                                        {
                                                                            cat: sc.id,
                                                                        }
                                                                    )}
                                                                >
                                                                    {ucfirst(sc.name)}
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
                    ) : null
                )}
            </div>
        </AppLayout>
    );
}
