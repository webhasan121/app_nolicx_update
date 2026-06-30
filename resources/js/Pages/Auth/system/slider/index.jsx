import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
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
import useTranslation from "../../../../hooks/useTranslation";
import { ActionIconButton, ActionIconLink } from "../../../../components/ActionIcon";

export default function Index({ nav = "web", slider = {}, filters = {}, updateable = null }) {
    const { t } = useTranslation();
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
        <AppLayout title={t("Slider")}>
            <Head title={t("Slider")} />

            <PageHeader>{t("Slider")}</PageHeader>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                <div className="flex flex-wrap items-center gap-3">
                                    <NavLink
                                        href={`?nav=web`}
                                        active={nav === "web"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("web");
                                        }}
                                    >{t("Web")}</NavLink>
                                    <NavLink
                                        href={`?nav=apps`}
                                        active={nav === "apps"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("apps");
                                        }}
                                    >{t("App")}</NavLink>
                                    <NavLink
                                        href={`?nav=both`}
                                        active={nav === "both"}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            changeNav("both");
                                        }}
                                    >{t("Both")}</NavLink>
                                </div>

                                <div className="flex w-full flex-col gap-2 sm:flex-row xl:w-auto">
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
                                        className="h-10 w-full py-2 xl:w-64"
                                        placeholder={t("Search sliders...")}
                                    />
                                    <SecondaryButton className="w-full justify-center sm:w-auto" onClick={() => setShowCreateModal(true)}>
                                        <i className="fas fa-plus pr-2"></i>{t("Add")}</SecondaryButton>
                                </div>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div>
                            <Table data={rows} tableClassName="min-w-[820px] xl:min-w-full">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{t("Name")}</th>
                                        <th>{t("Placement")}</th>
                                        <th>{t("Slides")}</th>
                                        <th></th>
                                        <th>{t("A/C")}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{(slider?.from ?? 1) + index}</td>
                                            <td>{item.name}</td>
                                            <td>{item.placement}</td>
                                            <td>{item.slides_count ?? 0}{t("Slides")}</td>
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
                                                <div className="flex items-center gap-1">
                                                    <ActionIconButton action="delete" title={t("Delete")} onClick={() => destroySlider(item)} />
                                                    <ActionIconButton action="edit" title={t("Edit")} onClick={() => openUpdateModal(item)} />
                                                    <ActionIconLink href={route("system.slider.slides", { id: item.id })} action="details" title={t("slides")} />
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>

                            {pagination.pages.length ? (
                                <div className="w-full pt-4">
                                    <div className="flex w-full flex-col gap-3 md:flex-row md:items-center md:justify-between">
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
                                                >{t("Previous")}</button>
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
                                                >{t("Next")}</button>
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
                <div className="px-2 py-2">{t("Slider Modal")}</div>
                <div className="p-3">
                    <strong></strong>
                    <form onSubmit={submitCreate}>
                        <div className="flex flex-col gap-2 sm:flex-row">
                            <TextInput
                                value={createForm.data.sliderName}
                                onChange={(e) => createForm.setData("sliderName", e.target.value)}
                                className="w-full py-2"
                                placeholder={t("Give Slider Name")}
                            />
                            <select
                                className="h-10 rounded border-gray-300 shadow sm:w-36"
                                value={createForm.data.sliderPlacement}
                                onChange={(e) => createForm.setData("sliderPlacement", e.target.value)}
                            >
                                <option value="web">{t("Web")}</option>
                                <option value="apps">{t("Apps")}</option>
                                <option value="both">{t("Both")}</option>
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
                        <div className="flex flex-col gap-2 sm:flex-row sm:justify-between">
                            <SecondaryButton
                                type="button"
                                className="mt-2 w-full justify-center sm:w-auto"
                                onClick={() => setShowCreateModal(false)}
                            >{t("Cancel")}</SecondaryButton>
                            <PrimaryButton className="mt-2 w-full justify-center sm:w-auto">{t("Add")}</PrimaryButton>
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
                <div className="px-3 py-2">{t("Edit Slider")}</div>
                <div className="p-3">
                    <form onSubmit={submitUpdate}>
                        <div>
                            <InputLabel>Name</InputLabel>
                            <TextInput
                                value={updateForm.data.name}
                                onChange={(e) => updateForm.setData("name", e.target.value)}
                                className="w-full py-2"
                                placeholder={t("Give Slider Name")}
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
                                <label htmlFor="Web">{t("For Web")}</label>
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
                                <label htmlFor="Web">{t("For Apps")}</label>
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
                                <label htmlFor="Web">{t("Both (Web & Apps)")}</label>
                            </div>
                        </div>

                        <div className="flex flex-col gap-2 sm:flex-row sm:justify-between">
                            <SecondaryButton
                                type="button"
                                className="mt-2 w-full justify-center sm:w-auto"
                                onClick={() => {
                                    setShowEditModal(false);
                                    router.get(route("system.slider.index"), { nav, find: search.trim() }, { preserveScroll: true });
                                }}
                            >{t("Cancel")}</SecondaryButton>
                            <PrimaryButton className="mt-2 w-full justify-center sm:w-auto">{t("Update")}</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
