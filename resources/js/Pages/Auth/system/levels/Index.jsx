import { router, useForm } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import InputLabel from "../../../../components/InputLabel";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

const emptyForm = {
    name: "",
    req_users: "",
    vip_users: "",
    bonus: "",
    rewards: "",
};

export default function Index({ columns = [], levels = [] }) {
    const [showModal, setShowModal] = useState(false);
    const [editingLevelId, setEditingLevelId] = useState(null);
    const form = useForm(emptyForm);

    const openCreateModal = () => {
        setEditingLevelId(null);
        form.clearErrors();
        form.setData(emptyForm);
        setShowModal(true);
    };

    const openEditModal = (level) => {
        setEditingLevelId(level.id);
        form.clearErrors();
        form.setData({
            name: level.name ?? "",
            req_users: level.req_users ?? "",
            vip_users: level.vip_users ?? "",
            bonus: level.bonus ?? "",
            rewards: level.rewards ?? "",
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingLevelId(null);
        form.clearErrors();
        form.setData(emptyForm);
    };

    const submit = (e) => {
        e.preventDefault();

        const url = editingLevelId
            ? route("system.levels.update", { level: editingLevelId })
            : route("system.levels.store");

        form.post(url, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    };

    const destroy = (levelId) => {
        if (!window.confirm("Delete this level?")) {
            return;
        }

        router.delete(route("system.levels.destroy", { level: levelId }), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            title="Star System - Levels"
            header={<PageHeader>Star System - Levels</PageHeader>}
        >
            <Container>
                <div className="flex items-center gap-2">
                    <NavLinkBtn href={route("system.levels.index")}>Levels</NavLinkBtn>
                    <NavLinkBtn href={route("system.levels.history")}>History</NavLinkBtn>
                </div>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <h2>Levels</h2>
                                <PrimaryButton type="button" onClick={openCreateModal}>
                                    <i className="mr-2 fas fa-plus"></i>
                                    <span>Add New</span>
                                </PrimaryButton>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        <div className="overflow-x-auto border border-gray-200 shadow-sm rounded-xl">
                            <table className="min-w-full text-sm divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {columns.map((column, index) => (
                                            <th
                                                key={`${column}-${index}`}
                                                className="px-4 py-3 font-medium text-left text-gray-600"
                                                width={index === columns.length - 1 ? "100" : undefined}
                                            >
                                                <strong>{column}</strong>
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-100">
                                    {levels.length ? (
                                        levels.map((level) => (
                                            <tr key={level.id} className="transition hover:bg-gray-50">
                                                <td className="px-4 py-3 font-medium text-gray-700">{level.sl}.</td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <strong className="px-3 py-1 text-white bg-blue-500 rounded-full hover:bg-blue-600">
                                                        {level.name}
                                                    </strong>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    <p className="flex items-center gap-2">
                                                        <strong>N. Users :</strong>
                                                        <span>{Number(level.req_users ?? 0).toLocaleString()}</span>
                                                    </p>
                                                    <p className="flex items-center gap-2">
                                                        <strong>VIP Users :</strong>
                                                        <span>{Number(level.vip_users ?? 0).toLocaleString()}</span>
                                                    </p>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    {Number(level.bonus ?? 0).toFixed(2)}
                                                </td>
                                                <td className="px-4 py-3 font-medium text-gray-700">
                                                    {level.rewards || "Not Available"}
                                                </td>
                                                <td className="px-4 py-3 space-x-2 text-center">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEditModal(level)}
                                                        className="inline-flex items-center justify-center p-2 text-xs font-medium text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 w-7 h-7"
                                                    >
                                                        <i className="fas fa-edit"></i>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        onClick={() => destroy(level.id)}
                                                        className="inline-flex items-center justify-center p-2 text-xs font-medium text-white transition bg-red-600 rounded-lg hover:bg-red-700 w-7 h-7"
                                                    >
                                                        <i className="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="px-4 py-6 text-center text-gray-500">
                                                <span>No levels found.</span>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showModal} onClose={closeModal} maxWidth="md">
                <div className="p-3">{`${editingLevelId ? "Update" : "Add New"} Level`}</div>
                <hr className="my-2" />
                <div className="p-4">
                    <form onSubmit={submit} className="space-y-6">
                        <div className="relative">
                            <InputLabel>Name of Level</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                                placeholder="Enter Level Name"
                            />
                            {form.errors.name ? (
                                <div className="mt-1 text-sm text-red-500">{form.errors.name}</div>
                            ) : null}
                        </div>

                        <div className="grid grid-cols-3 gap-6">
                            <div className="relative">
                                <InputLabel>Normal Users</InputLabel>
                                <TextInput
                                    type="number"
                                    className="w-full"
                                    value={form.data.req_users}
                                    onChange={(e) => form.setData("req_users", e.target.value)}
                                    placeholder="Required normal users"
                                />
                                {form.errors.req_users ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.req_users}</div>
                                ) : null}
                            </div>

                            <div className="relative">
                                <InputLabel>VIP Users</InputLabel>
                                <TextInput
                                    type="number"
                                    className="w-full"
                                    value={form.data.vip_users}
                                    onChange={(e) => form.setData("vip_users", e.target.value)}
                                    placeholder="Required vip users"
                                />
                                {form.errors.vip_users ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.vip_users}</div>
                                ) : null}
                            </div>

                            <div className="relative">
                                <InputLabel>Commission</InputLabel>
                                <TextInput
                                    type="number"
                                    step="0.01"
                                    className="w-full"
                                    value={form.data.bonus}
                                    onChange={(e) => form.setData("bonus", e.target.value)}
                                    placeholder="Enter commission"
                                />
                                {form.errors.bonus ? (
                                    <div className="mt-1 text-sm text-red-500">{form.errors.bonus}</div>
                                ) : null}
                            </div>
                        </div>

                        <div className="relative">
                            <InputLabel>Reward of Level</InputLabel>
                            <TextInput
                                type="text"
                                className="w-full"
                                value={form.data.rewards}
                                onChange={(e) => form.setData("rewards", e.target.value)}
                                placeholder="Enter level rewards"
                            />
                            {form.errors.rewards ? (
                                <div className="mt-1 text-sm text-red-500">{form.errors.rewards}</div>
                            ) : null}
                        </div>

                        <div className="flex justify-end">
                            <PrimaryButton type="submit" disabled={form.processing}>
                                Save Level
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
