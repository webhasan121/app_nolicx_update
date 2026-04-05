import { router, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Foreach from "../../../../components/dashboard/Foreach";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Div from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

const FILTERS = ["*", "Active", "Pending", "Disabled", "Suspended"];

export default function Index() {
    const { widgets = [], vendors = [], filter = "Active", find = "" } =
        usePage().props;
    const [search, setSearch] = useState(find ?? "");
    const [showFilterModal, setShowFilterModal] = useState(false);

    const visitWithQuery = (params) => {
        router.get(route("system.vendor.index"), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const runSearch = () => {
        visitWithQuery({
            filter,
            find: search,
        });
    };

    useEffect(() => {
        if ((find ?? "") === search) {
            return;
        }

        const timeout = setTimeout(() => {
            visitWithQuery({
                filter,
                find: search,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, filter]);

    return (
        <AppLayout title="Vendors" header={<PageHeader>Vendors</PageHeader>}>
            <div>
                <Container>
                    <SectionSection>
                        <OverviewSection>
                            {widgets.map((widget) => (
                                <Div
                                    key={widget.title}
                                    title={widget.title}
                                    content={widget.content}
                                />
                            ))}
                        </OverviewSection>
                    </SectionSection>

                    <SectionSection>
                        <div className="flex justify-between items-start">
                            <div>
                                {FILTERS.map((item) => (
                                    <NavLink
                                        key={item}
                                        href={`${route("system.vendor.index")}?filter=${encodeURIComponent(item)}`}
                                        className="px-2 mb-2"
                                        active={filter === item}
                                    >
                                        {item === "*" ? "All" : item}
                                    </NavLink>
                                ))}
                            </div>

                            <div>
                                <TextInput
                                    type="search"
                                    placeholder="Search Vendor"
                                    className="my-1 py-1 mr-1"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <PrimaryButton
                                    type="button"
                                    onClick={() => setShowFilterModal(true)}
                                >
                                    Filter
                                </PrimaryButton>
                            </div>
                        </div>

                        <SectionInner>
                            <Foreach data={vendors}>
                                <div>
                                    <Table data={vendors}>
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Status</th>
                                                <th>Commission</th>
                                                <th>Product</th>
                                                <th>Join</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {vendors.map((vendor, index) => (
                                            <tr key={vendor.id}>
                                                <td>{index + 1}</td>
                                                <td>{vendor.id}</td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {vendor.user_name}
                                                    </div>
                                                    <div className="badge badge-info text-nowrap">
                                                        {vendor.shop_name_en}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="text-nowrap">
                                                        {vendor.email}
                                                    </div>
                                                    <div className="text-nowrap">
                                                        {vendor.phone}
                                                    </div>
                                                    <div className="text-nowrap">
                                                        {vendor.location}
                                                    </div>
                                                </td>
                                                <td>{vendor.status}</td>
                                                <td>
                                                    <span className="badge badge-success">
                                                        {" "}
                                                        {vendor.system_get_comission}{" "}
                                                    </span>{" "}
                                                    %
                                                </td>
                                                <td>
                                                    <span className="badge badge-info">
                                                        {vendor.products_count}
                                                    </span>
                                                    <NavLink
                                                        href={route("system.products.index", {
                                                            find: vendor.id,
                                                            from: "vendor",
                                                        })}
                                                    >
                                                        View
                                                    </NavLink>
                                                </td>
                                                <td>{vendor.created_at_formatted}</td>
                                                <td>
                                                    <NavLink
                                                        href={route(
                                                            "system.vendor.settings",
                                                            {
                                                                id: vendor.id,
                                                            }
                                                        )}
                                                    >
                                                        Edit
                                                    </NavLink>
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

                <Modal
                    show={showFilterModal}
                    onClose={() => setShowFilterModal(false)}
                    maxWidth="2xl"
                >
                    <SectionSection className="h-screen overflow-y-scroll">
                        <SectionHeader
                            title="Filter Vendor"
                            content=""
                        />
                        <SectionInner className="overflow-y-scroll"></SectionInner>
                        <div className="mt-4">
                            <SecondaryButton
                                type="button"
                                onClick={() => setShowFilterModal(false)}
                            >
                                Close
                            </SecondaryButton>
                        </div>
                    </SectionSection>
                </Modal>
            </div>
        </AppLayout>
    );
}
