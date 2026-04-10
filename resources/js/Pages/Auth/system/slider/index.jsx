import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
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

export default function Index({ nav = "web", slider = {}, filters = {}, updateable = null }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(Boolean(updateable));
    const [search, setSearch] = useState(filters.find ?? "");
    const rows = slider.data ?? [];

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
                route("system.slider.index"),
                { nav, find: trimmedSearch },
                { preserveScroll: true, preserveState: true, replace: true }
            );
        }, 400);

        return () => clearTimeout(timeout);
    }, [search, nav]);

    const changeNav = (target) => {
        router.get(
            route("system.slider.index"),
            { nav: target, find: search.trim() },
            { preserveScroll: true }
        );
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

    const goToPage = (url) => {
        if (!url) {
            return;
        }

        const nextUrl = new URL(url);

        router.get(
            route("system.slider.index"),
            {
                nav: nextUrl.searchParams.get("nav") ?? nav,
                find: nextUrl.searchParams.get("find") ?? search,
                page: nextUrl.searchParams.get("page") ?? undefined,
                edit: nextUrl.searchParams.get("edit") ?? undefined,
            },
            { preserveScroll: true, preserveState: true, replace: true }
        );
    };

    const pagination = useMemo(() => {
        const links = slider?.links ?? [];

        return {
            prev: links[0] ?? null,
            next: links[links.length - 1] ?? null,
            pages: links.slice(1, -1),
        };
    }, [slider?.links]);

    const resultSummary =
        slider?.total > 0
            ? `Showing ${slider?.from ?? 0}-${slider?.to ?? 0} of ${slider?.total ?? 0} sliders`
            : "No sliders found";

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

                                <div className="flex items-center gap-2">
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
                                                route("system.slider.index"),
                                                { nav, find: search.trim() },
                                                {
                                                    preserveScroll: true,
                                                    preserveState: true,
                                                    replace: true,
                                                }
                                            );
                                        }}
                                        className="py-1"
                                        placeholder="Search sliders..."
                                    />
                                    <SecondaryButton onClick={() => setShowCreateModal(true)}>
                                        <i className="fas fa-plus pr-2"></i> Add
                                    </SecondaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div>
                            <Table data={rows}>
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
                                    {rows.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{(slider?.from ?? 1) + index}</td>
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
                        </div>
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
                    router.get(route("system.slider.index"), { nav, find: search.trim() }, { preserveScroll: true });
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
                                    router.get(route("system.slider.index"), { nav, find: search.trim() }, { preserveScroll: true });
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
