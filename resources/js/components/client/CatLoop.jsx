import { useState } from "react";
import NavLink from "../NavLink";

export default function CatLoop({ item, active = false, cat = "", style = "" }) {
    const [open, setOpen] = useState(true);

    if (!item || item.slug === "default-category") return null;
    const hasChildren = item.children && item.children.length > 0;
    const isActive = active || cat === item.slug;
    const label = item.name ? item.name.charAt(0).toUpperCase() + item.name.slice(1) : "";

    return (
        <div className="cat-item">
            <div className="py-1 text-lg">
                <div className={`flex items-center justify-between ${isActive ? "bg-white" : ""}`}>
                    <div className="flex-1 text-lg">
                        <NavLink
                            className={`${isActive ? "bg_secondary text-white" : ""} font-bold text-bold text-gray-900 text-md w-full inline-block`}
                            href={route("category.products", { cat: item.slug })}
                        >
                            <div className={style}>{label}</div>
                        </NavLink>
                    </div>
                    <div
                        className={`${hasChildren ? "" : "hidden"} text-sm text-gray-500 cursor-pointer`}
                        onClick={() => setOpen((v) => !v)}
                    >
                        {open ? (
                            <i className="text-gray-500 fas fa-chevron-down"></i>
                        ) : (
                            <i className={`${isActive ? "text-indigo-900" : ""} fas fa-chevron-right`}></i>
                        )}
                    </div>
                </div>
            </div>

            {hasChildren && open && (
                <div className="pl-2 border-l border-gray-900 ms-2">
                    {item.children.map((child) => (
                        <CatLoop
                            key={child.id}
                            item={child}
                            active={cat === child.slug}
                            cat={cat}
                        />
                    ))}
                </div>
            )}

        </div>
    );
}
