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