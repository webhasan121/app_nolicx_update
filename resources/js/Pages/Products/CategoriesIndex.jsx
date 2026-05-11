import Container from "../../components/dashboard/Container";
import NavLinkBtn from "../../components/NavLinkBtn";
import CatLoop from "../../components/client/CatLoop";
import UserLayout from "../../Layouts/User/App";
import NavLink from "../../components/NavLink";

export default function CategoriesIndex({ categories = [] }) {
    return (
        <UserLayout title="Category">
            <Container>
                <div>
                    <div>
                        <NavLinkBtn href={route("products.index")}>
                            All Product
                        </NavLinkBtn>
                        <br />
                    </div>

                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-10">
                    {categories
                        .filter((item) => item.slug !== "default-category")
                        .map((item) => (
                            <div
                                key={item.id}
                                className="text-center bg-white rounded-md cat_item"
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
                                        className="w-full h-full rounded-md"
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
