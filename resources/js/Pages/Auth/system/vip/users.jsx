import { router, usePage } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";

export default function Users() {
    const { vip, filters = {}, printUrl } = usePage().props;
    const [showFilterModal, setShowFilterModal] = useState(false);
    const [search, setSearch] = useState(filters.search ?? "");
    const [nav, setNav] = useState(filters.nav ?? "Pending");
    const [type, setType] = useState(filters.type ?? "All");
    const [validity, setValidity] = useState(filters.validity ?? "All");
    const [sdate, setSdate] = useState(filters.sdate ?? "");
    const [edate, setEdate] = useState(filters.edate ?? "");

    const applyFilters = (next = {}) => {
        router.get(
            route("system.vip.users"),
            {
                nav,
                search,
                sdate,
                edate,
                type,
                validity,
                ...next,
            },
            { preserveState: true, preserveScroll: true }
        );
    };

    return (
        <AppLayout
            title="VIP Users"
            header={
                <PageHeader>
                    <div className="md:flex items-center justify-between">
                        <div className="mb-1">VIP Users</div>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex flex-wrap justify-between items-start">
                                <div className="flex items-center gap-2">
                                    <SecondaryButton
                                        type="button"
                                        onClick={() => setShowFilterModal(true)}
                                    >
                                        <i className="fa-solid fa-filter"></i>
                                    </SecondaryButton>
                                    <select
                                        className="rounded-md py-1"
                                        value={nav}
                                        onChange={(e) => {
                                            setNav(e.target.value);
                                            applyFilters({ nav: e.target.value });
                                        }}
                                    >
                                        <option value="All">All</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Confirmed">Active</option>
                                        <option value="Trash">Trash</option>
                                    </select>
                                </div>
                                <div className="flex">
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <input
                                        type="search"
                                        className="ms-2 rounded-lg border-gray-400 py-1"
                                        placeholder="find name, id"
                                        value={search}
                                        onChange={(e) => {
                                            setSearch(e.target.value);
                                            applyFilters({ search: e.target.value });
                                        }}
                                    />
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <Foreach data={vip?.data ?? []}>
                            <Table data={vip?.data ?? []}>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>VIP</th>
                                        <th>Wallet</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Validity</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {(vip?.data ?? []).map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.sl}</td>
                                            <td>
                                                {item.name ?? "N/A"}
                                                <br />
                                                <div className="text-xs ">
                                                    {item.user_email ?? "N/A"}
                                                </div>
                                            </td>
                                            <td>
                                                {item.package_name ?? "N/A"}
                                                <div className="text-xs">
                                                    {" "}
                                                    {item.task_type ?? "N/A"}{" "}
                                                </div>
                                            </td>
                                            <td>{item.user_coin ?? "0"}</td>
                                            <td>
                                                {item.status}
                                                <br />
                                                {item.deleted_at_formatted ? (
                                                    <span className="text-xs text-red-900 text-bold ">
                                                        {item.deleted_at_formatted}
                                                    </span>
                                                ) : null}
                                            </td>
                                            <td>
                                                <div className="text-nowrap">
                                                    {item.created_at_formatted}
                                                </div>
                                            </td>
                                            <td>
                                                {item.valid_till_formatted}
                                                <div className="text-xs">
                                                    {item.valid_till_human}
                                                </div>
                                            </td>
                                            <td>
                                                <div className="flex space-x-3">
                                                    <NavLink
                                                        href={route("system.vip.edit", {
                                                            vip: item.id,
                                                        })}
                                                    >
                                                        View
                                                    </NavLink>
                                                    <NavLink href="#">User</NavLink>
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

            <Modal
                show={showFilterModal}
                onClose={() => setShowFilterModal(false)}
                maxWidth="2xl"
            >
                <div className="p-2">User Filter</div>
                <hr className="my-1" />
                <div className="p-3">
                    <div className="md:flex justify-between items-start mb-2 border-b">
                        <p>Taks Type</p>

                        <div className="md:flex items-center gap-2">
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="daily"
                                    value="daily"
                                    checked={type === "daily"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Daily Taks</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="monthly"
                                    value="monthly"
                                    checked={type === "monthly"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Monthly Taks</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="type_all"
                                    value="All"
                                    checked={type === "All"}
                                    onChange={(e) => setType(e.target.value)}
                                />
                                <p>Both</p>
                            </div>
                        </div>
                    </div>
                    <div className="md:flex items-start justify-between gap-2 mb-2 border-b">
                        <p>Package Validity</p>
                        <div className="md:flex items-center gap-2">
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="valid"
                                    value="valid"
                                    checked={validity === "valid"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Only Valid</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="invalid"
                                    value="invalid"
                                    checked={validity === "invalid"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Only Invalid</p>
                            </div>
                            <div className="flex items-center mb-2 border rounded-md p-2">
                                <input
                                    type="radio"
                                    className="w-4 h-4 rounded mr-2"
                                    id="validity_all"
                                    value="All"
                                    checked={validity === "All"}
                                    onChange={(e) => setValidity(e.target.value)}
                                />
                                <p>Both</p>
                            </div>
                        </div>
                    </div>
                    <div className="md:flex items-start justify-between gap-2">
                        <p>Between Date</p>
                        <div className="flex text-xs gap-2">
                            <TextInput
                                type="date"
                                className="py-1"
                                value={sdate}
                                onChange={(e) => setSdate(e.target.value)}
                            />
                            <TextInput
                                type="date"
                                className="py-1"
                                value={edate}
                                onChange={(e) => setEdate(e.target.value)}
                            />
                        </div>
                    </div>
                </div>
                <hr className="my-1" />
                <div className="p-3 flex gap-2">
                    <SecondaryButton
                        type="button"
                        onClick={() => setShowFilterModal(false)}
                    >
                        Close
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        onClick={() => {
                            setShowFilterModal(false);
                            applyFilters();
                        }}
                    >
                        Apply
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
