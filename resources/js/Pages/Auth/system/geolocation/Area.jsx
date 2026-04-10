import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
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

export default function Area({ filters, countries = [], states = [], cities = [], areas, printUrl }) {
    const [showModal, setShowModal] = useState(false);
    const filterForm = useForm({
        country: filters?.country ?? "",
        state_id: filters?.state_id ?? "",
        city_id: filters?.city_id ?? "",
        find: filters?.find ?? "",
    });
    const areaForm = useForm({
        country: filters?.country ?? "",
        state_id: filters?.state_id ?? "",
        city_id: filters?.city_id ?? "",
        area_name: "",
    });

    useEffect(() => {
        areaForm.setData((data) => ({
            ...data,
            country: filterForm.data.country,
            state_id: filterForm.data.state_id,
            city_id: filterForm.data.city_id,
        }));
    }, [filterForm.data.country, filterForm.data.state_id, filterForm.data.city_id]);

    const applyFilters = (next) => {
        router.get(route("system.geolocations.area"), next, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const updateCountry = (value) => {
        const next = {
            country: value,
            state_id: "",
            city_id: "",
            find: filterForm.data.find,
        };

        filterForm.setData(next);
        applyFilters(next);
    };

    const updateState = (value) => {
        const next = {
            country: filterForm.data.country,
            state_id: value,
            city_id: "",
            find: filterForm.data.find,
        };

        filterForm.setData(next);
        applyFilters(next);
    };

    const updateCity = (value) => {
        const next = {
            country: filterForm.data.country,
            state_id: filterForm.data.state_id,
            city_id: value,
            find: filterForm.data.find,
        };

        filterForm.setData(next);
        applyFilters(next);
    };

    const submitArea = (e) => {
        e.preventDefault();

        areaForm.post(route("system.geolocations.area.store"), {
            preserveScroll: true,
            onSuccess: () => {
                setShowModal(false);
                areaForm.setData((data) => ({
                    ...data,
                    area_name: "",
                }));
            },
        });
    };

    const destroyArea = (area) => {
        if (!window.confirm("Delete this area?")) {
            return;
        }

        router.delete(route("system.geolocations.area.destroy", { area: area.id }), {
            data: {
                country: filterForm.data.country,
                state_id: filterForm.data.state_id,
                city_id: filterForm.data.city_id,
            },
            preserveScroll: true,
        });
    };

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        applyFilters({
            country: nextUrl.searchParams.get("country") ?? filterForm.data.country,
            state_id: nextUrl.searchParams.get("state_id") ?? filterForm.data.state_id,
            city_id: nextUrl.searchParams.get("city_id") ?? filterForm.data.city_id,
            find: nextUrl.searchParams.get("find") ?? filterForm.data.find,
            page: nextUrl.searchParams.get("page") ?? undefined,
        });
    };

    const pagination = useMemo(() => {
        const links = areas?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [areas?.links]);

    const resultSummary =
        areas?.total > 0
            ? `Showing ${areas?.from ?? 0}-${areas?.to ?? 0} of ${areas?.total ?? 0} areas`
            : "No areas found";

    return (
        <AppLayout
            title="Geolocation - Cities"
            header={<PageHeader>Geolocation - Cities</PageHeader>}
        >
            <Head title="Geolocation - Cities" />

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
                                <div className="flex gap-2">
                                    <div>
                                        <InputLabel>Country</InputLabel>
                                        <select
                                            value={filterForm.data.country}
                                            onChange={(e) => updateCountry(e.target.value)}
                                            className="py-1 rounded-md"
                                        >
                                            <option value=""> -- Country -- </option>
                                            {countries.map((country) => (
                                                <option key={country.id} value={country.id}>
                                                    {country.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel>State</InputLabel>
                                        <select
                                            value={filterForm.data.state_id}
                                            onChange={(e) => updateState(e.target.value)}
                                            className="py-1 rounded-md"
                                        >
                                            <option value=""> -- State -- </option>
                                            {states.map((item) => (
                                                <option key={item.id} value={item.id}>
                                                    {item.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel>City</InputLabel>
                                        <select
                                            value={filterForm.data.city_id}
                                            onChange={(e) => updateCity(e.target.value)}
                                            className="py-1 rounded-md"
                                        >
                                            <option value=""> -- City -- </option>
                                            {cities.map((item) => (
                                                <option key={item.id} value={item.id}>
                                                    {item.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel>Search</InputLabel>
                                        <TextInput
                                            type="search"
                                            value={filterForm.data.find}
                                            onChange={(e) => {
                                                const value = e.target.value;

                                                filterForm.setData("find", value);
                                                applyFilters({
                                                    country: filterForm.data.country,
                                                    state_id: filterForm.data.state_id,
                                                    city_id: filterForm.data.city_id,
                                                    find: value,
                                                });
                                            }}
                                            className="py-1"
                                            placeholder="Search areas..."
                                        />
                                    </div>
                                </div>

                                <div className="flex items-center gap-2">
                                    <PrimaryButton
                                        type="button"
                                        onClick={() => window.open(printUrl, "_blank")}
                                    >
                                        <i className="fas fa-print"></i>
                                    </PrimaryButton>
                                    <PrimaryButton type="button" onClick={() => setShowModal(true)}>
                                        <i className="fas fa-plus mr-2"></i> Area
                                    </PrimaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        {(areas?.data ?? []).map((item, index) => (
                            <div key={item.id} className="flex justify-between items-center mb-2 p-2 shadow">
                                <div className="flex items-center">
                                    <div className="mr-2">{index + 1}</div>
                                    <div>{item.name}</div>
                                </div>
                                <div className="flex items-center gap-4">
                                    <DangerButton type="button" onClick={() => destroyArea(item)}>
                                        <i className="fas fa-trash"></i>
                                    </DangerButton>
                                    <NavLinkBtn href="">
                                        <i className="fas fa-angle-right"></i>
                                    </NavLinkBtn>
                                </div>
                            </div>
                        ))}

                        {filterForm.data.state_id && filterForm.data.city_id ? (
                            <div className="p-2 rounded-md border">
                                <p>Area Name</p>
                                <TextInput
                                    value={areaForm.data.area_name}
                                    onChange={(e) => areaForm.setData("area_name", e.target.value)}
                                    placeholder="Area Name"
                                    className="w-full"
                                />
                                {areaForm.errors.area_name ? (
                                    <span className="text-red-500 text-sm">{areaForm.errors.area_name}</span>
                                ) : null}
                                <div className="flex items-center justify-end my-2">
                                    <PrimaryButton type="button" onClick={submitArea}>Add</PrimaryButton>
                                </div>
                            </div>
                        ) : null}

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

            <Modal show={showModal} onClose={() => setShowModal(false)}>
                <div className="p-3">Add New City</div>
                <hr className="my-2" />

                <div className="p-3">
                    <form onSubmit={submitArea}>
                        <div className="mb-2 flex items-center gap-2">
                            <div className="mb-3">
                                <InputLabel>Country</InputLabel>
                                <select
                                    value={areaForm.data.country}
                                    onChange={(e) => {
                                        areaForm.setData((data) => ({
                                            ...data,
                                            country: e.target.value,
                                            state_id: "",
                                            city_id: "",
                                        }));
                                        updateCountry(e.target.value);
                                    }}
                                    className="py-1 rounded-md"
                                >
                                    <option value=""> -- Country -- </option>
                                    {countries.map((country) => (
                                        <option key={country.id} value={country.id}>
                                            {country.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="mb-3">
                                <InputLabel>State</InputLabel>
                                <select
                                    value={areaForm.data.state_id}
                                    onChange={(e) => {
                                        areaForm.setData((data) => ({
                                            ...data,
                                            state_id: e.target.value,
                                            city_id: "",
                                        }));
                                        updateState(e.target.value);
                                    }}
                                    className="py-1 rounded-md w-full"
                                >
                                    <option value=""> -- Select State -- </option>
                                    {states.map((item) => (
                                        <option key={item.id} value={item.id}>
                                            {item.name}
                                        </option>
                                    ))}
                                </select>
                                {areaForm.errors.state_id ? (
                                    <span className="text-red-500 text-sm">{areaForm.errors.state_id}</span>
                                ) : null}
                            </div>
                            <div className="mb-3">
                                <InputLabel>City</InputLabel>
                                <select
                                    value={areaForm.data.city_id}
                                    onChange={(e) => areaForm.setData("city_id", e.target.value)}
                                    className="py-1 rounded-md"
                                >
                                    <option value=""> -- City -- </option>
                                    {cities.map((item) => (
                                        <option key={item.id} value={item.id}>
                                            {item.name}
                                        </option>
                                    ))}
                                </select>
                                {areaForm.errors.city_id ? (
                                    <span className="text-red-500 text-sm">{areaForm.errors.city_id}</span>
                                ) : null}
                            </div>
                        </div>

                        <div className="mb-3">
                            <InputLabel>Area Name</InputLabel>
                            <TextInput
                                type="text"
                                value={areaForm.data.area_name}
                                onChange={(e) => areaForm.setData("area_name", e.target.value)}
                                className="w-full"
                                placeholder="Enter area Name"
                            />
                            {areaForm.errors.area_name ? (
                                <span className="text-red-500 text-sm">{areaForm.errors.area_name}</span>
                            ) : null}
                        </div>

                        <div className="flex justify-end">
                            <PrimaryButton type="submit">Save Area</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
