import { Link, usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";
import NavLink from "../../../components/NavLink";

export default function Comission() {
    const { nav = "earn", set = "com", rows = [], pagination } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Comissions"
                        content="Your Products comissions list."
                    />
                    <SectionInner>
                        <NavLink
                            href={route("user.wallet.earn-comissions", {
                                nav: "earn",
                            })}
                            active={nav === "earn"}
                        >
                            Earn Comissions
                        </NavLink>
                        <NavLink
                            href={route("user.wallet.earn-comissions", {
                                nav: "system",
                            })}
                            active={nav === "system"}
                        >
                            System Comissions
                        </NavLink>
                    </SectionInner>
                </SectionSection>

                <NavLink
                    href={route("user.wallet.earn-comissions", {
                        nav: "earn",
                        set: "com",
                    })}
                    active={set === "com"}
                >
                    Comissions
                </NavLink>
                <NavLink
                    href={route("user.wallet.earn-comissions", {
                        nav: "earn",
                        set: "prof",
                    })}
                    active={set === "prof"}
                >
                    Profits
                </NavLink>

                <div className="my-3">
                    {pagination?.links?.map((link, index) => (
                        <Link
                            key={index}
                            href={link.url || "#"}
                            className={`px-2 py-1 border ${
                                link.active ? "bg-indigo-900 text-white" : ""
                            } ${!link.url ? "pointer-events-none opacity-50" : ""}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>

                {nav === "earn" && set === "com" && (
                    <SectionSection>
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((earn, index) => (
                                    <tr key={earn.id}>
                                        <td>{index + 1}</td>
                                        <td>{earn.id}</td>
                                        <td>{earn.product}</td>
                                        <td>{earn.amount}</td>
                                        <td>{earn.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionSection>
                )}

                {set === "prof" && (
                    <Table data={rows}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Profit</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((earn, index) => (
                                <tr key={earn.id}>
                                    <td>{index + 1}</td>
                                    <td>{earn.id}</td>
                                    <td>{earn.product}</td>
                                    <td>{earn.profit}</td>
                                    <td>{earn.date}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                )}

                {nav === "system" && (
                    <Table data={rows}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Product</th>
                                <th>Order</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((take) => (
                                <tr key={take.id}>
                                    <td>{take.id}</td>
                                    <td>{take.amount}</td>
                                    <td>{take.product}</td>
                                    <td>{take.order}</td>
                                    <td>{take.date}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                )}
            </Container>
        </UserDash>
    );
}

