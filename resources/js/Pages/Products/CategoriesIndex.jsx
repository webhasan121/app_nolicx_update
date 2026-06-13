import Container from "../../components/dashboard/Container";
import NavLinkBtn from "../../components/NavLinkBtn";
import CatLoop from "../../components/client/CatLoop";
import UserLayout from "../../Layouts/User/App";
import NavLink from "../../components/NavLink";
import useTranslation from "../../hooks/useTranslation";

export default function CategoriesIndex({ categories = [] }) {
    const { t } = useTranslation();

    return (
        <UserLayout title={t("Category")}>
            <Container>
                <div className="py-4">
                    <div className="mb-3">
                        <NavLinkBtn href={route("products.index")}>
                            {t("All Product")}
                        </NavLinkBtn>
                    </div>

                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-10">
                    {categories
                        .filter((item) => item.slug !== "default-category")
                        .map((item) => (
                            <div
                                key={item.id}
                                className="relative overflow-hidden text-center bg-white rounded-md cat_item aspect-square"
                                style={{
                                    backdropFilter: "blur(3px)",
                                }}
                            >
                                <NavLink
                                    href={route("category.products", {
                                        cat: item.slug,
                                    })}
                                    className="flex flex-col items-center w-full h-full p-0 border-b-0 text-inherit hover:text-inherit hover:border-transparent"

                                >
                                    <img
                                        src={`/storage/${item.image}`}
                                        alt={item.name}
                                        className="object-cover w-full h-full rounded-md"
                                    />

                                    <div
                                        className="absolute bottom-0 w-full pt-1 text-center"
                                        style={{
                                            backgroundColor: "#f6f6f69c",
                                            backdropFilter: "blur(6px)",
                                        }}
                                    >
                                        {item.name.length > 9
                                            ? item.name.substring(0, 9) + "..."
                                            : item.name}
                                    </div>
                                </NavLink>
                            </div>
                        ))}
                    </div>
                </div>
            </Container>
        </UserLayout>
    );
}
