import NavLink from "../../components/NavLink";
import Hr from "../../components/Hr";
import Container from "../../components/dashboard/Container";
import ProductCard from "../../components/home/ProductCard";
import UserLayout from "../../Layouts/User/App";

export default function Show({ shop = {}, products = [] }) {
    return (
        <UserLayout title={shop?.shop_name_en ?? "Shop"}>
            <div>
                <div className="overflow-hidden bg-white">
                    <div className="relative">
                        {shop?.banner ? (
                            <img
                                className="w-full bg-indigo-900"
                                style={{ aspectRatio: "16/9" }}
                                src={`/storage/${shop.banner}`}
                                alt={shop?.shop_name_en ?? ""}
                            />
                        ) : null}
                        {shop?.logo ? (
                            <img
                                className="absolute top-0 left-0 m-2 bg-white rounded-full"
                                style={{ height: 80, width: 80 }}
                                src={`/storage/${shop.logo}`}
                                alt={shop?.shop_name_en ?? ""}
                            />
                        ) : null}
                    </div>

                    <Container>
                        <div>
                            <div className="flex flex-wrap gaps-10">
                                <div className="w-48 p-2 m-1 border rounded-lg">
                                    <p>Shop</p>
                                    <div>{shop?.shop_name_en}</div>
                                    <p className="text-xs">
                                        {shop?.village}, {shop?.upozila}, {shop?.district}
                                    </p>
                                    <div className="py-3">
                                        <div className="flex items-center">
                                            <i className="text-indigo-900 fas fa-star"></i>
                                            <i className="text-indigo-900 fas fa-star"></i>
                                            <i className="text-indigo-900 fas fa-star"></i>
                                            <i className="text-indigo-900 fas fa-star"></i>
                                            <i className="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div className="flex items-center justify-between mt-2 space-x-2 space-y-2">
                                        <div className="inline-block px-2 text-xs text-white rounded-lg bg-sky-900">
                                            reseller
                                        </div>
                                    </div>
                                </div>

                                <div className="w-48 p-2 m-1 border rounded-lg">
                                    <p>Owner</p>
                                    <div className="text-md">
                                        {shop?.user?.name}
                                    </div>
                                    <p className="text-xs">
                                        <i className="pr-3 fas fa-caret-right"></i> {shop?.email}
                                    </p>
                                    <p className="text-xs">
                                        <i className="pr-3 fas fa-caret-right"></i> {shop?.phone}
                                    </p>
                                    <p className="text-xs">
                                        {shop?.user?.village}, {shop?.user?.upozila}, {shop?.user?.district}
                                    </p>
                                </div>
                            </div>

                            <Hr />
                            <div className="flex justify-center space-x-3">
                                <div>
                                    <i className="fas fa-heart"></i>
                                </div>
                                <NavLink
                                    href={route("shops.visit", {
                                        id: shop?.id,
                                        name: shop?.shop_name_en ?? "not_found",
                                    })}
                                >
                                    Visit Shops <i className="px-2 fas fa-caret-right"></i>
                                </NavLink>
                            </div>
                        </div>
                    </Container>
                </div>
            </div>

            <Container>
                <div className="flex items-center justify-start py-3 mb-3">
                    <NavLink href="/">
                        <i className="fas fa-home pe-2"></i>
                    </NavLink>
                    <NavLink href={route("shops.reseller")}>
                        Shops
                    </NavLink>
                    <div className="text-gray-600">
                        {shop?.shop_name_en}
                    </div>
                </div>
            </Container>

            <Container className="my-[100]">
                <div>
                    <div className="w-full product_section md:w-3/4">
                        <div className="py-2 text-sm">Products</div>
                        {products?.length ? (
                            <div
                                style={{
                                    display: "grid",
                                    justifyContent: "start",
                                    gridTemplateColumns: "repeat(auto-fill, 160px)",
                                    gridGap: "10px",
                                }}
                            >
                                {products.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                        ) : null}
                    </div>
                </div>
            </Container>
        </UserLayout>
    );
}
