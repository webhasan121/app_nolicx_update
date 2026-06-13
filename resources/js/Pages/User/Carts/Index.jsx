import { router } from "@inertiajs/react";

import Container from "../../../components/dashboard/Container";
import NavLinkBtn from "../../../components/NavLinkBtn";
import UserDash from "../../../components/user/dash/UserDash";
import CartSummaryPanel from "../../../components/user/CartSummaryPanel";
import useTranslation from "../../../hooks/useTranslation";
import { ActionIconButton } from "../../../components/ActionIcon";

export default function Index({ carts }) {
    const { t } = useTranslation();
    const remove = (id) => {
        if (!confirm("Are you sure?")) return;
        router.delete(route("user.carts.remove", id), {
            preserveScroll: true,
        });
    };

    const total = carts.reduce((sum, item) => sum + Number(item.price), 0);
    const summaryItems = carts.map((cart) => ({
        id: cart.id,
        href: route("products.details", {
            id: cart.product?.id,
            slug: cart.product?.slug,
        }),
        image: cart.product?.thumbnail
            ? `/storage/${cart.product.thumbnail}`
            : "",
        name: cart.product?.name,
        shop: cart.product?.shop_name,
        quantity: cart.qty ?? 1,
        meta: cart.created_at_human,
        total: cart.price,
        priceText: `${cart.price || "N/A"} ${t("TK")}`,
    }));
    const notice = (
        <>
            <strong>{t("Notice:")}</strong> You're order from Multiple Shops.
            You have added product from more than one shop. Items from different
            shops are shipped separately, which will result in{" "}
            <strong>{t("Multiple Shipping Charges.")}</strong> To reduce
            delivery cost, place orders from{" "}
            <strong>{t("a single shop at a time.")}</strong>
        </>
    );

    return (
        <UserDash>
            <Container>
                <CartSummaryPanel
                    title={`${carts.length} items in cart`}
                    subtitle={
                        <NavLinkBtn href={route("user.carts.checkout")}>
                            checkout
                        </NavLinkBtn>
                    }
                    notice={notice}
                    items={summaryItems}
                    totals={[
                        {
                            label: t("Total"),
                            value: `${total} ${t("TK")}`,
                            emphasis: true,
                        },
                    ]}
                    renderAction={(cart) => (
                        <ActionIconButton
                            action="remove"
                            title={t("remove")}
                            onClick={() => remove(cart.id)}
                        />
                    )}
                />
            </Container>
        </UserDash>
    );
}
