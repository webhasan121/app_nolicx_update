import NavLink from "../NavLink";
import Hr from "../Hr";

export default function ShopsCart({ shop }) {
    if (!shop) return null;

    return (
        <div>
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="relative">
                    <img
                        className="w-full bg-indigo-900"
                        style={{ height: "100px" }}
                        src={`/storage/${shop.banner}`}
                        alt={shop.shop_name_en}
                    />
                    <img
                        className="absolute top-0 left-0 m-2 bg-white rounded-full"
                        style={{ height: "50px", width: "50px" }}
                        src={`/storage/${shop.logo}`}
                        alt={shop.shop_name_en}
                    />
                </div>
                <div className="p-3">
                    <div className="">{shop.shop_name_en}</div>
                    <p className="text-xs">
                        {shop.village}, {shop.upozila}, {shop.district}
                    </p>
                    <div className="mt-2 flex justify-between items-center space-x-2 space-y-2"></div>
                    <Hr />
                    <div className="flex justify-between">
                        <div>{/* keep same blade spacing block */}</div>
                        <NavLink
                            href={route("shops.visit", {
                                id: shop.id,
                                name: shop.shop_name_en ?? "not_found",
                            })}
                        >
                            Visit Shops <i className="px-2 fas fa-caret-right"></i>
                        </NavLink>
                    </div>
                </div>
            </div>
        </div>
    );
}
