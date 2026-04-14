import { usePage, router } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import Hr from "../../../../components/Hr";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import OverviewDiv from "../../../../components/dashboard/overview/Div";
import OverviewSection from "../../../../components/dashboard/overview/Section";
import CoinStore from "../../../../livewire/system/store/CoinStore";
import CoastStore from "../../../../livewire/system/store/CoastStore";
import DonationStore from "../../../../livewire/system/store/DonationStore";

export default function Index() {
    const {
        pageTitle = "Coin Store",
        widgets = [],
        tabs = [],
        activeTab = "commissions",
        columns1 = [],
        columns2 = [],
        storeMeta = {},
        coinStore = {},
        coastStore = {},
        donationStore = {},
        commissions = {},
        withdrawals = {},
    } = usePage().props;

    const targetStore = storeMeta?.target ?? {};
    const canDistribute = targetStore && targetStore.generate === false;

    const setTab = (tab) => {
        router.get(
            route("system.store.index"),
            { tab },
            { preserveScroll: true, preserveState: true }
        );
    };

    const distribute = () => {
        router.post(route("system.store.distribute"), {}, { preserveScroll: true });
    };

    return (
        <AppLayout title={pageTitle} header={<PageHeader>{pageTitle}</PageHeader>}>
            <Container>
                <section className="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    {widgets.map((widget, index) => (
                        <OverviewDiv
                            key={`${widget.label}-${index}`}
                            title={widget.label}
                            content={widget.value ?? 0}
                        />
                    ))}
                </section>
            </Container>

            <Hr />

            <Container>
                <section className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 mb-6">
                    <div className="relative bg-white rounded-md shadow-md p-6">
                        <CoinStore
                            store={coinStore.store}
                            take={coinStore.take}
                            give={coinStore.give}
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-6">
                        <div className="relative bg-white rounded-md shadow-md p-6">
                            <CoastStore store={coastStore.store} />
                        </div>
                        <div className="relative bg-white rounded-md shadow-md p-6">
                            <DonationStore store={donationStore.store} />
                        </div>
                    </div>
                </section>
            </Container>

            <Hr />

            <Container>
                <div className="flex gap-4 mt-6">
                    {tabs.map((tab) => (
                        <button
                            key={tab}
                            type="button"
                            onClick={() => setTab(tab)}
                            className={`px-3 py-2 rounded-md ${
                                activeTab === tab
                                    ? "bg-blue-500 text-white"
                                    : "bg-gray-200 text-gray-700"
                            }`}
                        >
                            {tab.charAt(0).toUpperCase() + tab.slice(1)}
                        </button>
                    ))}
                </div>

                {activeTab === "commissions" ? (
                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex justify-between items-center border-b pb-4">
                                    <h4>Distributed Commissions</h4>
                                    <div className="relative">
                                        {canDistribute ? (
                                            <button
                                                type="button"
                                                onClick={distribute}
                                                className="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-4 pb-1"
                                            >
                                                <span className="text-sm text-white">Distribute</span>
                                            </button>
                                        ) : (
                                            <div className="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-4 pb-1">
                                                <span className="text-sm text-white">Generated</span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            }
                            content=""
                        />

                        <SectionInner>
                            <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                                <table className="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {columns1.map((column, index) => (
                                                <th
                                                    key={`${column}-${index}`}
                                                    className="px-4 py-3 text-left font-semibold text-gray-600"
                                                >
                                                    <strong>{column}</strong>
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 bg-white">
                                        {(commissions?.data ?? []).length ? (
                                            commissions.data.map((item, index) => (
                                                <tr key={`${item.user_name}-${index}`} className="hover:bg-gray-50 transition">
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        {item.sl}
                                                    </td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        <strong>{item.user_name}</strong>
                                                    </td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.store}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.amount}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.range}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{item.info}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">
                                                        <span>-</span>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="7" className="px-4 py-6 text-center text-gray-500">
                                                    <span>No histories found.</span>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {(commissions?.links ?? []).length ? (
                                <div className="mt-4">
                                    {(commissions.links ?? []).map((link, index) => (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            disabled={!link.url}
                                            className={`px-3 py-1 mr-1 rounded ${
                                                link.active ? "bg-gray-200" : "bg-white"
                                            }`}
                                            onClick={() => link.url && router.get(link.url, {}, { preserveScroll: true })}
                                        >
                                            {link.label}
                                        </button>
                                    ))}
                                </div>
                            ) : null}
                        </SectionInner>
                    </SectionSection>
                ) : null}

                {activeTab === "withdrawals" ? (
                    <SectionSection>
                        <SectionHeader
                            title={<h4>Withdrawal History</h4>}
                            content=""
                        />

                        <SectionInner>
                            <div className="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                                <table className="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {columns2.map((column, index) => (
                                                <th
                                                    key={`${column}-${index}`}
                                                    className="px-4 py-3 text-left font-semibold text-gray-600"
                                                >
                                                    <strong>{column}</strong>
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 bg-white">
                                        {(withdrawals?.data ?? []).length ? (
                                            withdrawals.data.map((withdraw, index) => (
                                                <tr key={`${withdraw.user_name}-${index}`} className="hover:bg-gray-50 transition">
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.sl}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.user_name}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.store_req}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.maintenance_fee}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.server_fee}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.pay_by}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.status}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">{withdraw.requested_at}</td>
                                                    <td className="px-4 py-3 font-medium text-gray-700">-</td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="9" className="px-4 py-6 text-center text-gray-500">
                                                    <span>No histories found.</span>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {(withdrawals?.links ?? []).length ? (
                                <div className="mt-4">
                                    {(withdrawals.links ?? []).map((link, index) => (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            disabled={!link.url}
                                            className={`px-3 py-1 mr-1 rounded ${
                                                link.active ? "bg-gray-200" : "bg-white"
                                            }`}
                                            onClick={() => link.url && router.get(link.url, {}, { preserveScroll: true })}
                                        >
                                            {link.label}
                                        </button>
                                    ))}
                                </div>
                            ) : null}
                        </SectionInner>
                    </SectionSection>
                ) : null}
            </Container>
        </AppLayout>
    );
}
