import { Head, router, useForm } from "@inertiajs/react";
import { useEffect } from "react";
import AppLayout from "../../../../Layouts/App";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import DangerButton from "../../../../components/DangerButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import { useState } from "react";

export default function Cities({ filters, countries = [], states = [], cities }) {
    const [showModal, setShowModal] = useState(false);
    const filterForm = useForm({
        country: filters?.country ?? "",
        state_id: filters?.state_id ?? "",
    });
    const cityForm = useForm({
        country: filters?.country ?? "",
        state_id: filters?.state_id ?? "",
        city_name: "",
    });

    useEffect(() => {
        cityForm.setData((data) => ({
            ...data,
            country: filterForm.data.country,
            state_id: filterForm.data.state_id,
        }));
    }, [filterForm.data.country, filterForm.data.state_id]);

    const applyFilters = (next) => {
        router.get(
            route("system.geolocations.cities"),
            next,
            { preserveState: true, preserveScroll: true }
        );
    };

    const updateCountry = (value) => {
        const next = {
            country: value,
            state_id: "",
        };

        filterForm.setData(next);
        applyFilters(next);
    };

    const updateState = (value) => {
        const next = {
            country: filterForm.data.country,
            state_id: value,
        };

        filterForm.setData(next);
        applyFilters(next);
    };

    const submitCity = (e) => {
        e.preventDefault();

        cityForm.post(route("system.geolocations.cities.store"), {
            preserveScroll: true,
            onSuccess: () => {
                setShowModal(false);
                cityForm.setData((data) => ({
                    ...data,
                    city_name: "",
                }));
            },
        });
    };

    const destroyCity = (city) => {
        if (!window.confirm("Delete this city?")) {
            return;
        }

        router.delete(route("system.geolocations.cities.destroy", { city: city.id }), {
            data: {
                country: filterForm.data.country,
                state_id: filterForm.data.state_id,
            },
            preserveScroll: true,
        });
    };

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
                                            id="selectState"
                                        >
                                            <option value=""> -- State -- </option>
                                            {states.map((item) => (
                                                <option key={item.id} value={item.id}>
                                                    {item.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <PrimaryButton type="button" onClick={() => setShowModal(true)}>
                                    <i className="fas fa-plus mr-2"></i> City
                                </PrimaryButton>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        {(cities?.data ?? []).map((item, index) => (
                            <div key={item.id} className="flex justify-between items-center mb-2 p-2 shadow">
                                <div className="flex items-center">
                                    <div className="mr-2">
                                        {index + 1}
                                    </div>
                                    <div>{item.name}</div>
                                </div>
                                <div className="flex items-center gap-4">
                                    <DangerButton type="button" onClick={() => destroyCity(item)}>
                                        <i className="fas fa-trash"></i>
                                    </DangerButton>
                                    <NavLinkBtn
                                        href={route("system.geolocations.area", {
                                            state_id: filterForm.data.state_id,
                                            city_id: item.id,
                                        })}
                                    >
                                        <i className="fas fa-angle-right"></i>
                                    </NavLinkBtn>
                                </div>
                            </div>
                        ))}

                        {(cities?.links ?? []).length ? (
                            <div className="mt-4 flex flex-wrap items-center gap-2">
                                {cities.links.map((link, index) =>
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

            <Modal show={showModal} onClose={() => setShowModal(false)}>
                <div className="p-3">
                    Add New City
                </div>
                <hr className="my-2" />

                <div className="p-3">
                    <form onSubmit={submitCity}>
                        <div className="mb-2 flex items-center gap-2">
                            <div className="mb-3">
                                <InputLabel>Country</InputLabel>
                                <select
                                    value={cityForm.data.country}
                                    onChange={(e) => {
                                        cityForm.setData("country", e.target.value);
                                        cityForm.setData("state_id", "");
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
                                {cityForm.errors.country_id ? (
                                    <span className="text-red-500 text-sm">{cityForm.errors.country_id}</span>
                                ) : null}
                            </div>
                            <div className="mb-3">
                                <InputLabel>State</InputLabel>
                                <select
                                    value={cityForm.data.state_id}
                                    onChange={(e) => cityForm.setData("state_id", e.target.value)}
                                    className="py-1 rounded-md w-full"
                                >
                                    <option value=""> -- Select State -- </option>
                                    {states.map((item) => (
                                        <option key={item.id} value={item.id}>
                                            {item.name}
                                        </option>
                                    ))}
                                </select>
                                {cityForm.errors.state_id ? (
                                    <span className="text-red-500 text-sm">{cityForm.errors.state_id}</span>
                                ) : null}
                            </div>
                        </div>

                        <div className="mb-3">
                            <InputLabel>City Name</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                placeholder="Enter City Name"
                                value={cityForm.data.city_name}
                                onChange={(e) => cityForm.setData("city_name", e.target.value)}
                            />
                            {cityForm.errors.city_name ? (
                                <span className="text-red-500 text-sm">{cityForm.errors.city_name}</span>
                            ) : null}
                        </div>

                        <div className="flex justify-end">
                            <PrimaryButton type="submit">
                                Save City
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
