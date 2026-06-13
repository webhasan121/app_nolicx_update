import Div from "../../../components/dashboard/overview/Div";
import Section from "../../../components/dashboard/overview/Section";
import useTranslation from "../../../hooks/useTranslation";
import { formatAmount } from "../../../utils/formatAmount";

const money = (value) => `Tk ${formatAmount(value)}`;

export default function Overview({
    products,
    sales,
    today_sell,
    monthly_sell,
    product_stock,
    total_product_stock_price,
    yearly_sell_amount,
    total_amount,
    monthly_profit,
    daily_profit,
}) {
    const { t } = useTranslation();

    return (
        <>
            <p className="mb-2 text-xs">{t("Overall Details")}</p>
            <Section>
                <Div title={t("Products")} content={<div>{products ?? "0"}</div>} />
                <Div title={t("Sales")} content={<div>{money(sales)}</div>} />
                <Div title={t("Today sell")} content={<div>{money(today_sell)}</div>} />
                <Div title={t("Monthly sell")} content={<div>{money(monthly_sell)}</div>} />
                <Div title={t("Product stock")} content={<div>{product_stock ?? "0"}</div>} />
                <Div
                    title={t("Total product stock price")}
                    content={<div>{money(total_product_stock_price)}</div>}
                />
                <Div
                    title={t("Yearly sell amount")}
                    content={<div>{money(yearly_sell_amount)}</div>}
                />
                <Div title={t("Total amount")} content={<div>{money(total_amount)}</div>} />
                <Div title={t("Monthly profit")} content={<div>{money(monthly_profit)}</div>} />
                <Div title={t("Daily profit")} content={<div>{money(daily_profit)}</div>} />
            </Section>
            <hr className="my-2" />
        </>
    );
}
