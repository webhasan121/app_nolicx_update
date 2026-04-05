import { router, usePage } from "@inertiajs/react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import Container from "../../../../../components/dashboard/Container";
import Foreach from "../../../../../components/dashboard/Foreach";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index() {
    const { nav = "Active", packages = [] } = usePage().props;

    const handleTrash = (id) => {
        router.post(route("system.vip.trash", { id }));
    };

    const handleRestore = (id) => {
        router.post(route("system.vip.restore", { id }));
    };

    return (
        <AppLayout
            title="VIP"
            header={
                <PageHeader>
                    VIP
                    <br />
                    <div>
                        <NavLink href={route("system.vip.index")} active={route().current("system.vip.index")}>
                            <i className="fa-solid fa-up-right-from-square me-2"></i> Package
                        </NavLink>
                        <NavLink href={route("system.vip.users")} active={route().current("system.vip.users")}>
                            <i className="fa-solid fa-up-right-from-square me-2"></i> User
                        </NavLink>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <NavLinkBtn href={route("system.vip.crate")}>
                                New
                            </NavLinkBtn>
                        }
                        content={
                            <>
                                <NavLink href={route("system.vip.index", { nav: "Active" })} active={nav === "Active"}>
                                    Active
                                </NavLink>
                                <NavLink href={route("system.vip.index", { nav: "Trash" })} active={nav === "Trash"}>
                                    Trash
                                </NavLink>
                            </>
                        }
                    />

                    <SectionInner>
                        <Foreach data={packages}>
                            <Table data={packages}>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Timer</th>
                                        <th>Coin</th>
                                        <th>Sell</th>
                                        <th>Earn</th>
                                        <th>Created</th>
                                        <th>A/C</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {packages.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{index + 1}</td>
                                            <td>
                                                <div className="position-relative">
                                                    {item.name}
                                                </div>
                                            </td>
                                            <td>{item.price} TK</td>
                                            <td>{item.countdown} Minute</td>
                                            <td>
                                                <div>D - {item.coin}</div>
                                                <div>M - {item.m_coin}</div>
                                                <hr className="my-1" />
                                                <div>Ref - {item.ref_owner_get_coin}</div>
                                            </td>
                                            <td>{item.users_count ?? "0"}</td>
                                            <td>{item.earn}</td>
                                            <td>
                                                <div>{item.created_at_human}</div>
                                                <div className="text-xs">
                                                    {item.created_at_formatted}
                                                </div>
                                            </td>
                                            <td>
                                                <div className="flex">
                                                    <NavLinkBtn
                                                        href={route(
                                                            "system.package.edit",
                                                            { packages: item.id }
                                                        )}
                                                        className="me-2"
                                                    >
                                                        View
                                                    </NavLinkBtn>

                                                    {nav === "Trash" ? (
                                                        <NavLinkBtn
                                                            href="#"
                                                            onClick={(e) => {
                                                                e.preventDefault();
                                                                handleRestore(item.id);
                                                            }}
                                                        >
                                                            Restore
                                                        </NavLinkBtn>
                                                    ) : (
                                                        <DangerButton
                                                            type="button"
                                                            onClick={() => handleTrash(item.id)}
                                                        >
                                                            Trash
                                                        </DangerButton>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Foreach>
                    </SectionInner>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
