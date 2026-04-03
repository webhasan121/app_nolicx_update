import { router, usePage } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";

export default function Index() {
    const { widgets = [], users, filters = {}, printUrl } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? "");
    const [sd, setSd] = useState(filters.sd ?? "");
    const [ed, setEd] = useState(filters.ed ?? "");
    const [showFilterModal, setShowFilterModal] = useState(false);

    const applyFilters = () => {
        router.get(
            route("system.users.view"),
            { search, sd, ed },
            { preserveState: true, preserveScroll: true }
        );
    };

    return (
        <AppLayout
            title="Users"
            header={<PageHeader>Users</PageHeader>}
        >
            <div>
                <Container>
                    <SectionSection>
                        <div className="grid grid-cols-6 gap-6">
                            {widgets.map((widget) => (
                                <Div
                                    key={widget.head}
                                    title={widget.head}
                                    content={widget.data}
                                />
                            ))}
                        </div>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title=""
                            content={
                                <div className="flex justify-between items-center gap-2">
                                    <div>
                                        <PrimaryButton
                                            type="button"
                                            onClick={() => window.open(printUrl, "_blank")}
                                        >
                                            <i className="fas fa-print"></i>
                                        </PrimaryButton>
                                    </div>
                                    <div className="flex gap-2">
                                        <TextInput
                                            type="date"
                                            className="py-1"
                                            value={sd}
                                            onChange={(e) => setSd(e.target.value)}
                                        />
                                        <TextInput
                                            type="date"
                                            className="py-1"
                                            value={ed}
                                            onChange={(e) => setEd(e.target.value)}
                                        />
                                        <TextInput
                                            type="search"
                                            placeholder="search"
                                            className="py-1"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key === "Enter") {
                                                    applyFilters();
                                                }
                                            }}
                                        />
                                    </div>
                                </div>
                            }
                        />

                        <SectionInner>
                            {users?.links?.length ? (
                                <div className="mb-3 flex flex-wrap items-center gap-2">
                                    {users.links.map((link, index) =>
                                        link.url ? (
                                            <button
                                                key={`${link.label}-${index}`}
                                                type="button"
                                                className={`px-3 py-1 border rounded ${link.active ? "bg-orange-500 text-white border-orange-500" : "bg-white"}`}
                                                onClick={() =>
                                                    router.visit(link.url, {
                                                        preserveState: true,
                                                        preserveScroll: true,
                                                    })
                                                }
                                            >
                                                {link.label}
                                            </button>
                                        ) : (
                                            <span
                                                key={`${link.label}-${index}`}
                                                className="px-3 py-1 border rounded text-gray-400"
                                            >
                                                {link.label}
                                            </span>
                                        )
                                    )}
                                </div>
                            ) : null}

                            <Foreach data={users?.data ?? []}>
                                <div>
                                    <Table data={users?.data ?? []}>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Ref & Reference</th>
                                                <th>Role</th>
                                                <th>Permissions</th>
                                                <th>VIP</th>
                                                <th>Order</th>
                                                <th>Wallet</th>
                                                <th>Created</th>
                                                <th>A/C</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {(users?.data ?? []).map((user, index) => (
                                                <tr key={user.id}>
                                                    <td>{(users?.from ?? 1) + index}</td>
                                                    <td>{user.id ?? "N/A"}</td>
                                                    <td>
                                                        {user.name ?? "N/A"}
                                                        <br />
                                                        <b className="text-xs">
                                                            {user.email ?? "N/A"}
                                                        </b>
                                                    </td>
                                                    <td>
                                                        {user.ref ?? "N/A"}
                                                        <br />
                                                        <span className="px-2 text-xs rounded border">
                                                            {user.reference ?? "Not Found"} &gt;{" "}
                                                            {user.reference_owner_name}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div className="flex">
                                                            {user.roles.map((role) => (
                                                                <div
                                                                    key={`${user.id}-${role}`}
                                                                    className="px-1 rounded border m-1 text-sm"
                                                                >
                                                                    {role}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </td>
                                                    <td>{user.permissions_count}</td>
                                                    <td>
                                                        <div className={user.vip_status.className}>
                                                            {user.vip_status.label}
                                                        </div>
                                                    </td>
                                                    <td>{user.orders_count}</td>
                                                    <td>{user.coin}</td>
                                                    <td>{user.created_at_formatted}</td>
                                                    <td>
                                                        <div className="flex">
                                                            <NavLink
                                                                href={route(
                                                                    "system.users.edit",
                                                                    {
                                                                        id: user.id,
                                                                    }
                                                                )}
                                                            >
                                                                <i className="fa-solid fa-pen mr-2"></i> Edit
                                                            </NavLink>
                                                            <NavLink href="#">
                                                                <i className="fa-solid fa-eye mr-2"></i> view
                                                            </NavLink>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </Table>
                                </div>
                            </Foreach>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </div>

            <Modal
                show={showFilterModal}
                onClose={() => setShowFilterModal(false)}
                maxWidth="xl"
            >
                <div className="p-4">
                    <div className="text-lg">Filter Users</div>
                    <div className="flex flex-col gap-2"></div>
                    <div className="mt-4">
                        <SecondaryButton
                            type="button"
                            onClick={() => setShowFilterModal(false)}
                        >
                            Close
                        </SecondaryButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
