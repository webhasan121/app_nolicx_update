import Div from "../../../components/dashboard/overview/Div";
import Section from "../../../components/dashboard/overview/Section";

const money = (value) => `Tk ${Number(value ?? 0).toLocaleString()}`;

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
    return (
        <>
            <p className="mb-2 text-xs">Overall Details</p>
            <Section>
                <Div title="Products" content={<div>{products ?? "0"}</div>} />
                <Div title="Sales" content={<div>{money(sales)}</div>} />
                <Div title="Today sell" content={<div>{money(today_sell)}</div>} />
                <Div title="Monthly sell" content={<div>{money(monthly_sell)}</div>} />
                <Div title="Product stock" content={<div>{product_stock ?? "0"}</div>} />
                <Div
                    title="Total product stock price"
                    content={<div>{money(total_product_stock_price)}</div>}
                />
                <Div
                    title="Yearly sell amount"
                    content={<div>{money(yearly_sell_amount)}</div>}
                />
                <Div title="Total amount" content={<div>{money(total_amount)}</div>} />
                <Div title="Monthly profit" content={<div>{money(monthly_profit)}</div>} />
                <Div title="Daily profit" content={<div>{money(daily_profit)}</div>} />
            </Section>
            <hr className="my-2" />
        </>
    );
}
