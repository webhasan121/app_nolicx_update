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

const FILTERS = ["Active", "Pending", "Disabled", "Suspended"];

export default function Index() {
    const {
        widgets = [],
        resellers = {},
        filter = "Active",
        find = "",
    } = usePage().props;
    const [search, setSearch] = useState(find ?? "");
    const [showFilterModal, setShowFilterModal] = useState(false);
    const rows = resellers.data ?? [];

    const visitWithQuery = (params) => {
        router.get(route("system.reseller.index"), params, {
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

    const gotoPage = (page) => {
        visitWithQuery({
            filter,
            find: search,
            page,
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
                page: 1,
            });
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, filter]);

    return (
        <AppLayout title="Resellers" header={<PageHeader>Resellers</PageHeader>}>
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
                        <SectionHeader
                            title={
                                <div className="flex justify-between items-start">
                                    <div>
                                        {FILTERS.map((item) => (
                                            <NavLink
                                                key={item}
                                                href={`${route("system.reseller.index")}?filter=${encodeURIComponent(item)}`}
                                                className="px-2 mb-2"
                                                active={filter === item}
                                            >
                                                {item}
                                            </NavLink>
                                        ))}
                                    </div>

                                    <div>
                                        <TextInput
                                            type="search"
                                            placeholder="Search Vendor"
                                            className="my-1 py-1"
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
                            }
                            content=""
                        />
                        <SectionInner>
                            <Foreach data={rows}>
                                <div>
                                    <Table data={rows}>
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Commission</th>
                                                <th>Category</th>
                                                <th>Product</th>
                                                <th>Join</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {rows.map((item, index) => (
                                                <tr key={item.id}>
                                                    <td>{index + 1}</td>
                                                    <td>
                                                        {item.user_name}
                                                        <br />
                                                        <span className="text-xs">
                                                            {item.shop_name_bn}
                                                        </span>
                                                    </td>
                                                    <td>{item.status}</td>
                                                    <td>{item.system_get_comission}</td>
                                                    <td>{item.categories_count}</td>
                                                    <td>{item.products_count}</td>
                                                    <td>
                                                        {item.created_at_human}
                                                        <br />
                                                        <span className="text-xs">
                                                            {item.created_at_formatted}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <NavLink
                                                            href={route("system.reseller.edit", {
                                                                id: item.id,
                                                                filter,
                                                            })}
                                                        >
                                                            edit
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
                        <SectionHeader title="Filter Reseller" content="" />
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
