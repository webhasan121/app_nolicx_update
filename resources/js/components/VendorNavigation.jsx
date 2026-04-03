import ResponsiveNavLink from "@/Components/ResponsiveNavLink";
import Hr from "@/Components/Hr";

export function VendorNavigation() {
    return (
        <>
            <ResponsiveNavLink href={route('my-shop')}>
                <i className="w-6 pr-2 fas fa-shop" /> My Shop
            </ResponsiveNavLink>

            <Hr />

            <ResponsiveNavLink href={route('vendor.products.view')}>
                <i className="pr-2 fas fa-layer-group" /> Products
            </ResponsiveNavLink>

            <ResponsiveNavLink href={route('vendor.orders.index')}>
                <i className="w-6 pr-2 fas fa-sort" /> Orders
            </ResponsiveNavLink>
        </>
    );
}
