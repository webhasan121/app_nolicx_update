import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function States({ states, countries = [], filters = {}, printUrl }) {
    const [showModal, setShowModal] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const [search, setSearch] = useState(filters.find ?? "");
    const form = useForm({
        stateId: "",
        name: "",
        country_id: "",
        country_code: "",
        iso2: "",
        iso3166_2: "",
    });

    useEffect(() => {
        setSearch(filters.find ?? "");
    }, [filters.find]);

    useEffect(() => {
        const trimmedSearch = search.trim();
        const currentSearch = (filters.find ?? "").trim();

        if (trimmedSearch === currentSearch) {
            return;
        }

        const timeout = setTimeout(() => {
            router.get(
                route("system.geolocations.states"),
                { find: trimmedSearch },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [search]);

    const syncCountryCode = (countryId) => {
        const selectedCountry = countries.find((item) => String(item.id) === String(countryId));
        form.setData((data) => ({
            ...data,
            country_id: countryId,
            country_code: selectedCountry?.iso2 ?? "",
        }));
    };

    const openCreate = () => {
        form.setData({
            stateId: "",
            name: "",
            country_id: "",
            country_code: "",
            iso2: "",
            iso3166_2: "",
        });
        form.clearErrors();
        setIsEdit(false);
        setShowModal(true);
    };

    const openEdit = (state) => {
        form.setData({
            stateId: state.id,
            name: state.name ?? "",
            country_id: state.country_id ?? "",
            country_code: state.country_code ?? "",
            iso2: state.iso2 ?? "",
            iso3166_2: state.iso3166_2 ?? "",
        });
        form.clearErrors();
        setIsEdit(true);
        setShowModal(true);
    };

    const closeModal = () => {
        if (form.processing) {
            return;
        }

        setShowModal(false);
    };

    const submit = (e) => {
        e.preventDefault();

        const url = isEdit
            ? `/dashboard/system/geolocations/states/${form.data.stateId}`
            : "/dashboard/system/geolocations/states";

        form.post(url, {
            preserveScroll: true,
            onSuccess: () => {
                setShowModal(false);
            },
        });
    };

    const destroy = (state) => {
        if (!window.confirm("Delete this state?")) {
            return;
        }

        router.delete(`/dashboard/system/geolocations/states/${state.id}`, {
            preserveScroll: true,
        });
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("system.geolocations.states"),
            {
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    };

    const pagination = useMemo(() => {
        const links = states?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [states?.links]);

    const resultSummary =
        states?.total > 0
            ? `Showing ${states?.from ?? 0}-${states?.to ?? 0} of ${states?.total ?? 0} states`
            : "No states found";

    return (
        <AppLayout
            title="Geolocation - States"
            header={<PageHeader>Geolocation - States</PageHeader>}
        >
            <Head title="Geolocation - States" />

            <Container>
                <div className="flex items-center gap-2">
                    <NavLinkBtn href={route("system.geolocations.countries")}>Countries</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.states")}>States</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.cities")}>Cities</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.area")}>Areas</NavLinkBtn>
                </div>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <h2>States</h2>
                                <div className="flex flex-wrap items-center justify-end gap-2">
                                    <TextInput
                                        type="search"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key !== "Enter") {
                                                return;
                                            }

                                            e.preventDefault();
                                            router.get(
                                                route("system.geolocations.states"),
                                                { find: search.trim() },
                                                {
                                                    preserveState: true,
                                                    preserveScroll: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="py-1"
                                        placeholder="Search states..."
                                    />
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <PrimaryButton type="button" onClick={openCreate}>
                                        <i className="fas fa-plus mr-2"></i>
                                        <span>Add New</span>
                                    </PrimaryButton>
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
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">SL No.</th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">
                                            Name of State
                                        </th>
                                        <th className="px-4 py-3 text-left font-semibold text-gray-600">
                                            Country Name
                                        </th>
                                        <th className="px-4 py-3 text-center font-semibold text-gray-600">ISO2</th>
                                        <th className="px-4 py-3 text-center font-semibold text-gray-600">ISO3</th>
                                        <th
                                            className="px-4 py-3 text-left font-semibold text-gray-600"
                                            width="100"
                                        >
                                            A/C
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {(states?.data ?? []).length ? (
                                        (states?.data ?? []).map((state) => (
                                            <tr key={state.id} className="hover:bg-gray-50 transition">
                                                <td className="px-4 py-3 font-medium text-gray-700">{state.sl}</td>
                                                <td className="px-4 py-3 text-gray-700">{state.name}</td>
                                                <td className="px-4 py-3 text-gray-700">{state.country_name}</td>
                                                <td className="px-4 py-3 text-center">
                                                    <span className="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold">
                                                        {state.iso2}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <span className="px-2 py-1 rounded bg-purple-50 text-purple-700 text-xs font-semibold">
                                                        {state.iso3166_2}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-center space-x-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEdit(state)}
                                                        className="inline-flex justify-center items-center p-2 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 w-7 h-7 transition"
                                                    >
                                                        <i className="fas fa-edit"></i>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        onClick={() => destroy(state)}
                                                        className="inline-flex justify-center items-center p-2 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 w-7 h-7 transition"
                                                    >
                                                        <i className="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="6" className="px-4 py-6 text-center text-gray-500">
                                                <span>No countries found.</span>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {pagination.pages.length ? (
                            <div className="w-full pt-4">
                                <div className="flex w-full items-center justify-between gap-3">
                                    <div className="text-sm text-slate-700">
                                        {resultSummary}
                                    </div>
                                    <div className="flex items-center md:justify-end">
                                        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                            <button
                                                type="button"
                                                disabled={!pagination.prev?.url}
                                                className="border-r border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.prev?.url)}
                                            >
                                                Previous
                                            </button>
                                            {pagination.pages.map((link, index) => (
                                                <button
                                                    key={`${link.label}-${index}`}
                                                    type="button"
                                                    disabled={!link.url}
                                                    className={`min-w-10 border-r border-slate-200 px-4 py-2 text-sm font-semibold transition ${
                                                        link.active
                                                            ? "bg-slate-100 text-blue-600"
                                                            : "bg-white text-slate-700 hover:bg-slate-50"
                                                    } disabled:cursor-not-allowed disabled:opacity-50`}
                                                    onClick={() => goToPage(link.url)}
                                                >
                                                    {link.label}
                                                </button>
                                            ))}
                                            <button
                                                type="button"
                                                disabled={!pagination.next?.url}
                                                className="px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                                                onClick={() => goToPage(pagination.next?.url)}
                                            >
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showModal} onClose={closeModal} maxWidth="md">
                <div className="p-3">{`${isEdit ? "Update" : "Add New"} State`}</div>
                <hr className="my-2" />
                <div className="p-4">
                    <form onSubmit={submit} className="space-y-6">
                        <div className="relative">
                            <InputLabel>Name of State</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                placeholder="Enter state Name"
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                            />
                            {form.errors.name ? <span className="text-red-500 text-sm">{form.errors.name}</span> : null}
                        </div>
                        <div className="relative">
                            <InputLabel>Country Name</InputLabel>
                            <select
                                value={form.data.country_id}
                                onChange={(e) => syncCountryCode(e.target.value)}
                                className="py-1 rounded-md"
                                id="selectCountry"
                            >
                                <option value=""> -- Country -- </option>
                                {countries.map((item) => (
                                    <option key={item.id} value={item.id}>
                                        {item.name}
                                    </option>
                                ))}
                            </select>
                            {form.errors.country_id ? (
                                <span className="text-red-500 text-sm">{form.errors.country_id}</span>
                            ) : null}
                        </div>
                        <div className="grid grid-cols-2 gap-6">
                            <div className="relative">
                                <InputLabel>Code (ISO2)</InputLabel>
                                <TextInput
                                    type="text"
                                    className="w-full"
                                    placeholder="Enter iso2 code"
                                    value={form.data.iso2}
                                    onChange={(e) => form.setData("iso2", e.target.value)}
                                />
                                {form.errors.iso2 ? <span className="text-red-500 text-sm">{form.errors.iso2}</span> : null}
                            </div>
                            <div className="relative">
                                <InputLabel>Code (ISO3)</InputLabel>
                                <TextInput
                                    type="text"
                                    className="w-full"
                                    placeholder="Enter iso3 code"
                                    value={form.data.iso3166_2}
                                    onChange={(e) => form.setData("iso3166_2", e.target.value)}
                                />
                                {form.errors.iso3166_2 ? (
                                    <span className="text-red-500 text-sm">{form.errors.iso3166_2}</span>
                                ) : null}
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <PrimaryButton type="submit" disabled={form.processing}>
                                Save State
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
