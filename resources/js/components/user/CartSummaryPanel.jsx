import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";

function formatMoney(value) {
    const number = Number(value ?? 0);
    return Number.isFinite(number) ? number.toLocaleString("en-US") : value;
}

export default function CartSummaryPanel({
    title = "Cart Summary",
    subtitle,
    notice,
    items = [],
    totals = [],
    renderQuantity,
    renderAttributes,
    renderAction,
}) {
    const { t } = useTranslation();

    return (
        <div className="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-lg">
            <div className="p-4 border-b border-gray-100 sm:p-5">
                <div className="items-start justify-between gap-4 sm:flex">
                    <div>
                        <h2 className="text-2xl font-semibold leading-tight text-gray-900">
                            {title}
                        </h2>
                        {subtitle ? (
                            <div className="mt-1 text-sm text-gray-600">
                                {subtitle}
                            </div>
                        ) : null}
                    </div>

                    <div className="px-3 py-1 mt-3 text-xs font-semibold text-orange-700 border border-orange-200 rounded-full bg-orange-50 sm:mt-0">
                        {items.length} {items.length === 1 ? t("item") : t("items")}
                    </div>
                </div>

                {notice ? (
                    <div className="flex gap-3 p-3 mt-4 text-sm border rounded-md border-amber-200 bg-amber-50 text-amber-900">
                        <i className="mt-1 fas fa-circle-info"></i>
                        <div className="leading-relaxed">{notice}</div>
                    </div>
                ) : null}
            </div>

            <div className="hidden grid-cols-12 gap-3 px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 md:grid">
                <div className="col-span-5">{t("Product")}</div>
                <div className="col-span-2">{t("Shop")}</div>
                <div className="col-span-2">{t("Quantity")}</div>
                <div className="col-span-1">{t("Attr")}</div>
                <div className="col-span-2 text-right">{t("Price")}</div>
            </div>

            <div className="divide-y divide-gray-100">
                {items.length ? items.map((item, index) => (
                    <div
                        key={item.id ?? index}
                        className="grid gap-3 p-4 transition md:grid-cols-12 md:items-center hover:bg-gray-50"
                    >
                        <div className="flex min-w-0 gap-3 md:col-span-5">
                            <div className="flex items-center justify-center w-6 text-xs font-semibold text-gray-400 shrink-0">
                                {index + 1}
                            </div>
                            {item.image ? (
                                <img
                                    width="52"
                                    height="52"
                                    src={item.image}
                                    alt=""
                                    className="object-cover w-14 h-14 border border-gray-200 rounded-md bg-gray-50 shrink-0"
                                />
                            ) : (
                                <div className="flex items-center justify-center w-14 h-14 text-gray-400 border border-gray-200 rounded-md bg-gray-50 shrink-0">
                                    <i className="fas fa-image"></i>
                                </div>
                            )}
                            <div className="min-w-0">
                                {item.href ? (
                                    <NavLink
                                        href={item.href}
                                        className="block text-sm font-medium leading-5 text-gray-800 hover:text-orange-600"
                                    >
                                        {item.name || "N/A"}
                                    </NavLink>
                                ) : (
                                    <div className="text-sm font-medium leading-5 text-gray-800">
                                        {item.name || "N/A"}
                                    </div>
                                )}
                                {item.meta ? (
                                    <div className="mt-1 text-xs text-gray-500">
                                        {item.meta}
                                    </div>
                                ) : null}
                            </div>
                        </div>

                        <div className="text-sm text-gray-600 md:col-span-2">
                            <span className="font-semibold text-gray-500 md:hidden">
                                {t("Shop")}:{" "}
                            </span>
                            {item.shop || "N/A"}
                        </div>

                        <div className="md:col-span-2">
                            {renderQuantity ? (
                                renderQuantity(item, index)
                            ) : (
                                <span className="inline-flex items-center px-3 py-1 text-sm font-semibold text-gray-700 rounded-md bg-gray-100">
                                    {item.quantity ?? 1}
                                </span>
                            )}
                        </div>

                        <div className="text-sm text-gray-600 md:col-span-1">
                            {renderAttributes ? renderAttributes(item, index) : item.attribute || "-"}
                        </div>

                        <div className="flex items-center justify-between gap-3 md:block md:col-span-2 md:text-right">
                            <div>
                                <span className="font-semibold text-gray-500 md:hidden">
                                    {t("Price")}:{" "}
                                </span>
                                <span className="text-sm font-semibold text-gray-900">
                                    {item.priceText ?? `${formatMoney(item.total ?? item.price)} TK`}
                                </span>
                            </div>
                            {renderAction ? renderAction(item, index) : null}
                        </div>
                    </div>
                )) : (
                    <div className="p-8 text-sm text-center text-gray-500">
                        {t("No cart item found.")}
                    </div>
                )}
            </div>

            {totals.length ? (
                <div className="grid gap-3 p-4 border-t border-gray-100 bg-gray-50 md:grid-cols-3">
                    {totals.map((total) => (
                        <div
                            key={total.label}
                            className={`p-3 bg-white border rounded-md ${
                                total.emphasis
                                    ? "border-orange-200 shadow-sm"
                                    : "border-gray-200"
                            }`}
                        >
                            <div className="text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                {total.label}
                            </div>
                            <div
                                className={`mt-1 text-lg font-semibold ${
                                    total.emphasis
                                        ? "text-orange-700"
                                        : "text-gray-900"
                                }`}
                            >
                                {total.value}
                            </div>
                        </div>
                    ))}
                </div>
            ) : null}
        </div>
    );
}
