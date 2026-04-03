import { useState } from "react";
import NavLink from "../NavLink";

export default function Chr({
    item,
    loop = 1,
    collapse = false,
    onDelete,
}) {
    const [open, setOpen] = useState(collapse);
    const children = item?.children ?? [];
    const productCount = item?.products?.length ?? item?.products_count ?? 0;

    return (
        <div className="p-2 border-b border-gray-200 w-full hover:bg-gray-50 cursor-pointer flex justify-between items-start">
            <div className="flex-1 ">
                <div className="flex gap-2">
                    <span className="pr-2">{loop}</span>
                    <img
                        src={`/storage/${item?.image ?? ""}`}
                        width="30"
                        height="30"
                        alt=""
                    />
                    {item?.name
                        ? item.name.charAt(0).toUpperCase() + item.name.slice(1)
                        : ""}
                </div>

                {open ? (
                    <div className="w-full">
                        {children.length > 0 ? (
                            <div className="px-2 py-1 border-l w-full">
                                {children.map((child, index) => (
                                    <Chr
                                        key={child.id ?? index}
                                        item={child}
                                        loop={index + 1}
                                        collapse={collapse}
                                        onDelete={onDelete}
                                    />
                                ))}
                            </div>
                        ) : (
                            <span className="text-sm text-gray-500">
                                No Child
                            </span>
                        )}
                    </div>
                ) : null}
            </div>

            <div className="flex items-center">
                <div className="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs mr-2">
                    {productCount} Products
                </div>
                <button
                    type="button"
                    className="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs mr-2"
                    onClick={() => setOpen((value) => !value)}
                >
                    {children.length} Child{" "}
                    <i
                        className={`fas ${
                            open ? "fa-caret-down" : "fa-caret-right"
                        }`}
                    ></i>
                </button>
                <NavLink
                    href={route("system.categories.edit", { cid: item?.id })}
                    className="text-blue-500 hover:underline mr-2"
                >
                    <i className="fas fa-edit"></i>
                </NavLink>
                <button
                    type="button"
                    onClick={() => onDelete?.(item?.id)}
                    className="text-red-500 hover:underline"
                >
                    <i className="fas fa-trash"></i>
                </button>
            </div>
        </div>
    );
}
