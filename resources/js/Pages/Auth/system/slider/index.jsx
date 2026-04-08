import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({ nav = "web", slider = [], updateable = null }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(Boolean(updateable));

    const createForm = useForm({
        sliderName: "",
        sliderPlacement: "web",
        status: true,
        sliderImage: null,
        background_color: "",
        nav,
    });

    const updateForm = useForm({
        id: updateable?.id ?? "",
        name: updateable?.name ?? "",
        placement: updateable?.placement ?? "web",
        nav,
    });

    useEffect(() => {
        setShowEditModal(Boolean(updateable));
        updateForm.setData({
            id: updateable?.id ?? "",
            name: updateable?.name ?? "",
            placement: updateable?.placement ?? "web",
            nav,
        });
    }, [updateable, nav]);

    const changeNav = (target) => {
        router.get(route("system.slider.index"), { nav: target }, { preserveScroll: true });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post(route("system.slider.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
                createForm.setData("sliderPlacement", "web");
                createForm.setData("status", true);
                createForm.setData("nav", nav);
            },
        });
    };

    const openUpdateModal = (item) => {
        router.get(
            route("system.slider.index"),
            { nav, edit: item.id },
            { preserveScroll: true, preserveState: false }
        );
    };

    const submitUpdate = (e) => {
        e.preventDefault();
        updateForm.post(route("system.slider.update", { slider: updateForm.data.id }), {
            preserveScroll: true,
        });
    };

    const updateStatus = (item, status) => {
        router.post(
            route("system.slider.status", { slider: item.id }) + `?nav=${encodeURIComponent(nav)}`,
            { status }
        );
    };

    const destroySlider = (item) => {
        if (!window.confirm("Are you sure you want to delete this slider?")) {
            return;
        }

        router.delete(
            route("system.slider.destroy", { slider: item.id }) + `?nav=${encodeURIComponent(nav)}`
        );
    };

    return (
        <AppLayout title="Slider">
            <Head title="Slider" />

            <PageHeader>Slider</PageHeader>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <div>
                                    <NavLink
                                        href={`?nav=web`}
                                        active={nav === "web"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("web");
                                        }}
                                    >
                                        Web
                                    </NavLink>
                                    <NavLink
                                        href={`?nav=apps`}
                                        active={nav === "apps"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("apps");
                                        }}
                                    >
                                        App
                                    </NavLink>
                                    <NavLink
                                        href={`?nav=both`}
                                        active={nav === "both"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("both");
                                        }}
                                    >
                                        Both
                                    </NavLink>
                                </div>

                                <SecondaryButton onClick={() => setShowCreateModal(true)}>
                                    <i className="fas fa-plus pr-2"></i> Add
                                </SecondaryButton>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <Table data={slider}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Placement</th>
                                    <th>Slides</th>
                                    <th></th>
                                    <th>A/C</th>
                                </tr>
                            </thead>
                            <tbody>
                                {slider.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.name}</td>
                                        <td>{item.placement}</td>
                                        <td>{item.slides_count ?? 0} Slides</td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                checked={Boolean(item.status)}
                                                onChange={(e) => updateStatus(item, e.target.checked)}
                                                style={{ width: 20, height: 20 }}
                                            />{" "}
                                            {item.status ? "Active" : "Deactive"}
                                        </td>
                                        <td>
                                            <div className="flex space-x-2">
                                                <DangerButton onClick={() => destroySlider(item)}>
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>

                                                <PrimaryButton onClick={() => openUpdateModal(item)}>
                                                    <i className="fas fa-edit"></i>
                                                </PrimaryButton>
                                                <NavLink href={route("system.slider.slides", { id: item.id })}>
                                                    slides
                                                </NavLink>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)} maxWidth="sm">
                <div className="px-2 py-2">Slider Modal</div>
                <div className="p-3">
                    <strong></strong>
                    <form onSubmit={submitCreate}>
                        <div className="flex">
                            <TextInput
                                value={createForm.data.sliderName}
                                onChange={(e) => createForm.setData("sliderName", e.target.value)}
                                className="rounded-0 py-1 w-full"
                                placeholder="Give Slider Name"
                            />
                            <select
                                className="py-1 rounded shadow"
                                value={createForm.data.sliderPlacement}
                                onChange={(e) => createForm.setData("sliderPlacement", e.target.value)}
                            >
                                <option value="web">Web</option>
                                <option value="apps">Apps</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        {createForm.errors.sliderName ? (
                            <span className="text-xs text-red-900">{createForm.errors.sliderName}</span>
                        ) : null}

                        <div className="flex justify-start items-center my-2 border-t border-b py-2">
                            <input
                                type="checkbox"
                                id="active"
                                checked={createForm.data.status}
                                onChange={(e) => createForm.setData("status", e.target.checked)}
                                width="25px"
                                height="25px"
                                className="me-3"
                            />
                            <InputLabel className="py-0 my-0" htmlFor="active">
                                Active Now
                            </InputLabel>
                        </div>
                        {createForm.errors.status ? (
                            <span className="text-xs text-red-900">{createForm.errors.status}</span>
                        ) : null}
                        <div className="flex justify-between">
                            <SecondaryButton
                                type="button"
                                className="mt-2"
                                onClick={() => setShowCreateModal(false)}
                            >
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton className="mt-2">Add</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>

            <Modal
                show={showEditModal}
                onClose={() => {
                    setShowEditModal(false);
                    router.get(route("system.slider.index"), { nav }, { preserveScroll: true });
                }}
                maxWidth="sm"
            >
                <div className="px-3 py-2">Edit Slider</div>
                <div className="p-3">
                    <form onSubmit={submitUpdate}>
                        <div>
                            <InputLabel>Name</InputLabel>
                            <TextInput
                                value={updateForm.data.name}
                                onChange={(e) => updateForm.setData("name", e.target.value)}
                                className="rounded-0 py-1 w-full"
                                placeholder="Give Slider Name"
                            />
                        </div>

                        <div className="py-2">
                            <div className="flex py-1 border rounded px-2 mb-1">
                                <input
                                    type="radio"
                                    checked={updateForm.data.placement === "web"}
                                    onChange={() => updateForm.setData("placement", "web")}
                                    value="web"
                                    className="h-5 w-5 me-3"
                                    id="web"
                                />
                                <label htmlFor="Web">For Web</label>
                            </div>
                            <div className="flex py-1 border rounded px-2 mb-1">
                                <input
                                    type="radio"
                                    checked={updateForm.data.placement === "apps"}
                                    onChange={() => updateForm.setData("placement", "apps")}
                                    value="apps"
                                    className="h-5 w-5 me-3"
                                    id="apps"
                                />
                                <label htmlFor="Web">For Apps</label>
                            </div>
                            <div className="flex py-1 border rounded px-2 mb-1">
                                <input
                                    type="radio"
                                    checked={updateForm.data.placement === "both"}
                                    onChange={() => updateForm.setData("placement", "both")}
                                    value="both"
                                    className="h-5 w-5 me-3"
                                    id="both"
                                />
                                <label htmlFor="Web">Both (Web & Apps) </label>
                            </div>
                        </div>

                        <div className="flex justify-between">
                            <SecondaryButton
                                type="button"
                                className="mt-2"
                                onClick={() => {
                                    setShowEditModal(false);
                                    router.get(route("system.slider.index"), { nav }, { preserveScroll: true });
                                }}
                            >
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton className="mt-2">Update</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
