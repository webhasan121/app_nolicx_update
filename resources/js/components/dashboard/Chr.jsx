import { useState } from "react";
import { ActionIconButton, ActionIconLink } from "../ActionIcon";

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
        <div className="w-full border-b border-gray-200 p-3 transition hover:bg-gray-50">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div className="min-w-0 flex-1">
                    <div className="flex items-start gap-3">
                        <span className="w-5 shrink-0 pt-1 text-sm font-medium text-slate-700">
                            {loop}
                        </span>
                        <img
                            src={`/storage/${item?.image ?? ""}`}
                            width="30"
                            height="30"
                            alt=""
                            className="h-8 w-8 shrink-0 rounded object-cover"
                        />
                        <div className="min-w-0">
                            <div className="break-words text-base font-medium text-slate-800">
                                {item?.name
                                    ? item.name.charAt(0).toUpperCase() + item.name.slice(1)
                                    : ""}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-2 sm:justify-end">
                    <div className="rounded-full bg-gray-100 px-3 py-1 text-center text-xs text-gray-600">
                        {productCount} Products
                    </div>
                    <button
                        type="button"
                        className="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600"
                        onClick={() => setOpen((value) => !value)}
                    >
                        {children.length} Child{" "}
                        <i
                            className={`fas ${
                                open ? "fa-caret-down" : "fa-caret-right"
                            }`}
                        ></i>
                    </button>
                    <div className="flex items-center gap-1">
                        <ActionIconLink
                            href={route("system.categories.edit", { cid: item?.id })}
                            action="edit"
                            title="Edit"
                        />
                        <ActionIconButton
                            action="delete"
                            title="Delete"
                            onClick={() => onDelete?.(item?.id)}
                        />
                    </div>
                </div>
            </div>

            {open ? (
                <div className="w-full pt-3">
                    {children.length > 0 ? (
                        <div className="w-full border-l pl-3 sm:pl-4">
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
    );
}
