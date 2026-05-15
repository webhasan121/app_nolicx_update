import Container from "../../components/dashboard/Container";
import VendorOrdersIndex from "../../livewire/vendor/orders/Index";
import Overview from "./overview/Overview";

export default function Vendor({ vendorOverview, vendorOrdersIndex, activeNav }) {
    return (
        <>
            <Container>
                <Overview
                    products={vendorOverview?.products}
                    sales={vendorOverview?.sales}
                    today_sell={vendorOverview?.today_sell}
                    monthly_sell={vendorOverview?.monthly_sell}
                    product_stock={vendorOverview?.product_stock}
                    total_product_stock_price={vendorOverview?.total_product_stock_price}
                    yearly_sell_amount={vendorOverview?.yearly_sell_amount}
                    total_amount={vendorOverview?.total_amount}
                    monthly_profit={vendorOverview?.monthly_profit}
                    daily_profit={vendorOverview?.daily_profit}
                />
            </Container>

            <Container>
                <p className="text-xs mb-2">Recent Orders</p>
            </Container>

            <VendorOrdersIndex
                orderIndex={vendorOrdersIndex}
                activeNav={activeNav}
            />
        </>
    );
}
