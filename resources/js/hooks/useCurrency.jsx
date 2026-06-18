import { usePage } from "@inertiajs/react";
import { formatAmount } from "../utils/formatAmount";

export default function useCurrency() {
    const { appConfig = {} } = usePage().props;
    const currency = appConfig?.currency ?? {
        code: "BDT",
        name: "Bangladeshi Taka",
        symbol: "TK",
    };

    const symbol = currency?.symbol || "TK";

    return {
        currency,
        symbol,
        code: currency?.code || "BDT",
        format: (value, decimals = 2) => `${formatAmount(value, decimals)} ${symbol}`,
    };
}
