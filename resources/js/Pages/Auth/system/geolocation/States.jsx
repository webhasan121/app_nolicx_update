import { Head, router, useForm } from "@inertiajs/react";
import { useState } from "react";
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

export default function States({ states, countries = [] }) {
    const [showModal, setShowModal] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const form = useForm({
        stateId: "",
        name: "",
        country_id: "",
        country_code: "",
        iso2: "",
        iso3166_2: "",
    });

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
                                <PrimaryButton type="button" onClick={openCreate}>
                                    <i className="fas fa-plus mr-2"></i>
                                    <span>Add New</span>
                                </PrimaryButton>
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

                        {(states?.links ?? []).length ? (
                            <div className="mt-4 flex flex-wrap items-center gap-2">
                                {states.links.map((link, index) =>
                                    link.url ? (
                                        <button
                                            key={`${link.label}-${index}`}
                                            type="button"
                                            className={`px-3 py-1 border rounded ${
                                                link.active ? "bg-orange-500 text-white border-orange-500" : "bg-white"
                                            }`}
                                            onClick={() =>
                                                router.visit(link.url, {
                                                    preserveState: true,
                                                    preserveScroll: true,
                                                })
                                            }
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span
                                            key={`${link.label}-${index}`}
                                            className="px-3 py-1 border rounded text-gray-400"
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    )
                                )}
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
