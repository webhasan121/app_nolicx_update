import { router, Link } from "@inertiajs/react";

import Slot from "../../../components/Slot";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import DashboardForeach from "../../../components/DashboardForeach";
import NavLinkBtn from "../../../components/NavLinkBtn";
import DangerButton from "../../../components/DangerButton";
import UserDash from "../../../components/user/dash/UserDash";
import Table from "../../../components/dashboard/table/Table";

export default function Index({ carts }) {
    const remove = (id) => {
        if (!confirm("Are you sure?")) return;
        router.delete(route("user.carts.remove", id), {
            preserveScroll: true,
        });
    };

    const total = carts.reduce((sum, item) => sum + Number(item.price), 0);

    return (
        <UserDash>
            <Container>
                {/* Notice Section */}
                <SectionSection>
                    <Slot
                        title={
                            <>
                                <b>Notice:</b> You're order from Multiple Shops
                            </>
                        }
                        content={
                            <>
                                You have added product from more than one shop.
                                Please note that, items from different shops are
                                shipped separately, which will result in{" "}
                                <strong>Multiple Shipping Charges.</strong>
                                <br />
                                To reduce delivery cost and ensure a smoother
                                experience, we recommend placing orders from{" "}
                                <strong>a single shop at a time.</strong> Review
                                the shop name in your cart before placing
                                orders.
                            </>
                        }
                    />
                </SectionSection>

                {/* Cart Section */}
                <SectionSection>
                    <SectionHeader
                        title={`${carts.length} items in cart`}
                        content={
                            <NavLinkBtn href={route("user.carts.checkout")}>
                                checkout
                            </NavLinkBtn>
                        }
                    />

                    <SectionInner>
                        <DashboardForeach data={carts}>
                            <div className="overflow-hidden overflow-x-scroll">
                                <table className="w-full mb-2 border border-collapse">
                                    <style>
                                        {`
          thead th {
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            font-size: 15px;
            text-align: left;
          }

          td {
            padding: 12px;
            font-size: 14px;
          }
        `}
                                    </style>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>product</th>
                                            <th>Shop</th>
                                            <th>price</th>
                                            <th>date</th>
                                            <th>A/C</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {carts.map((cart, index) => (
                                            <tr key={cart.id}>
                                                <td></td>
                                                <td>{index + 1}</td>

                                                <td>
                                                    <Link
                                                        className="text-xs"
                                                        href={route(
                                                            "products.details",
                                                            {
                                                                id: cart.product
                                                                    ?.id,
                                                                slug: cart
                                                                    .product
                                                                    ?.slug,
                                                            },
                                                        )}
                                                    >
                                                        <img
                                                            width="30"
                                                            height="30"
                                                            src={`/storage/${cart.product?.thumbnail}`}
                                                            alt=""
                                                        />
                                                        {cart.product?.name ||
                                                            "N/A"}
                                                    </Link>
                                                </td>

                                                <td className="text-xs">
                                                    {cart.product?.shop_name ||
                                                        "N/A"}
                                                </td>

                                                <td>{cart.price || "N/A"}</td>

                                                <td>
                                                    {cart.created_at_human ||
                                                        "N/A"}
                                                </td>

                                                <td>
                                                    <DangerButton
                                                        type="button"
                                                        onClick={() =>
                                                            remove(cart.id)
                                                        }
                                                    >
                                                        remove
                                                    </DangerButton>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>Total</td>
                                            <td></td>
                                            <td className="bold">
                                                <strong>{total} TK</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </DashboardForeach>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
