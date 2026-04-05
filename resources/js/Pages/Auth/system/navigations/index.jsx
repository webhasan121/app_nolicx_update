import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({
    menus = [],
    selectedMenu = null,
    selectedMenuItems = [],
}) {
    const [isOpenAddMenuForm, setIsOpenAddMenuForm] = useState(false);
    const [showMenuModal, setShowMenuModal] = useState(Boolean(selectedMenu));
    const [activeMenu, setActiveMenu] = useState(selectedMenu);
    const [menuItems, setMenuItems] = useState(selectedMenuItems);

    const addMenuForm = useForm({
        name: "",
    });

    const renameForm = useForm({
        name: selectedMenu?.name ?? "",
    });

    useEffect(() => {
        setShowMenuModal(Boolean(selectedMenu));
        setActiveMenu(selectedMenu);
        setMenuItems(selectedMenuItems);
        renameForm.setData("name", selectedMenu?.name ?? "");
    }, [selectedMenu, selectedMenuItems]);

    const submitNewMenu = (e) => {
        e.preventDefault();
        addMenuForm.post(route("system.navigations.menus.store"));
    };

    const openMenu = (menu) => {
        router.get(
            route("system.navigations.index"),
            { menu: menu.id },
            { preserveScroll: true, preserveState: false }
        );
    };

    const destroyMenu = (id) => {
        if (!window.confirm("Are you sure you want to delete this menu?")) {
            return;
        }

        router.delete(route("system.navigations.menus.destroy", { menu: id }));
    };

    const submitRename = (e) => {
        e.preventDefault();
        if (!activeMenu) return;
        renameForm.post(route("system.navigations.menus.rename", { menu: activeMenu.id }));
    };

    const addNewMenuItem = () => {
        if (!activeMenu) return;
        setMenuItems((items) => [
            ...items,
            { name: "", url: "", navigations_id: activeMenu.id },
        ]);
    };

    const updateMenuItemField = (index, field, value) => {
        setMenuItems((items) =>
            items.map((item, key) =>
                key === index ? { ...item, [field]: value } : item
            )
        );
    };

    const destroyMenuItem = (index) => {
        const item = menuItems[index];
        if (item?.id) {
            router.delete(route("system.navigations.items.destroy", { item: item.id }));
            return;
        }

        setMenuItems((items) => items.filter((_, key) => key !== index));
    };

    const updateMenuItems = () => {
        if (!activeMenu) return;
        router.post(route("system.navigations.items.update", { menu: activeMenu.id }), {
            items: menuItems,
        });
    };

    return (
        <AppLayout title="Navigations">
            <Head title="Navigations" />

            <PageHeader>Navigations</PageHeader>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>Menus</div>
                                <SecondaryButton onClick={() => setIsOpenAddMenuForm((v) => !v)}>
                                    <i className="fa-solid fa-plus pe-2"></i> New
                                </SecondaryButton>
                            </div>
                        }
                        content=""
                    />
                    <SectionInner>
                        {isOpenAddMenuForm ? (
                            <div className="p-3 border rounded shadow-lg mx:w-48">
                                <form onSubmit={submitNewMenu}>
                                    <InputLabel>Menu Name</InputLabel>
                                    <div className="flex flex-wrap">
                                        <TextInput
                                            value={addMenuForm.data.name}
                                            onChange={(e) => addMenuForm.setData("name", e.target.value)}
                                            placeholder="Menu Name"
                                            className="py-1"
                                        />
                                        <PrimaryButton className="m-1">Save</PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        ) : null}
                    </SectionInner>
                </Section>

                <Section>
                    <Table data={menus}>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Menu</th>
                                <th>Items</th>
                                <th>A/C</th>
                            </tr>
                        </thead>
                        <tbody>
                            {menus.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.name ?? "N/A"}</td>
                                    <td>{item.links?.length ?? 0}</td>
                                    <td>
                                        <div className="flex space-x-2">
                                            <DangerButton onClick={() => destroyMenu(item.id)}>
                                                <i className="fas fa-trash"></i>
                                            </DangerButton>
                                            <SecondaryButton onClick={() => openMenu(item)}>
                                                View
                                            </SecondaryButton>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </Section>
            </Container>

            <Modal
                show={showMenuModal}
                onClose={() => {
                    setShowMenuModal(false);
                    router.get(route("system.navigations.index"));
                }}
            >
                <div className="p-4 overflow-y-scroll h-100%">
                    <div className="py-2 border-b">
                        <form onSubmit={submitRename}>
                            <TextInput
                                value={renameForm.data.name}
                                onChange={(e) => renameForm.setData("name", e.target.value)}
                            />
                            <button type="submit">Update</button>
                        </form>
                    </div>
                    <div className="py-2">
                        <div className="flex items-center justify-between mb-2"></div>
                        <div className="space-y-3">
                            {menuItems.map((item, key) => (
                                <div key={item.id ?? key} className="p-2 space-y-2 rounded shadow">
                                    <TextInput
                                        placeholder="Menu Item Name"
                                        value={item.name ?? ""}
                                        onChange={(e) =>
                                            updateMenuItemField(key, "name", e.target.value)
                                        }
                                        className="py-1 text-sm  rounded-0"
                                    />
                                    <div>
                                        <TextInput
                                            placeholder="Menu Item URL"
                                            value={item.url ?? ""}
                                            onChange={(e) =>
                                                updateMenuItemField(key, "url", e.target.value)
                                            }
                                            className="w-full py-1 text-sm  rounded-0"
                                        />
                                    </div>
                                    <DangerButton onClick={() => destroyMenuItem(key)}>
                                        <i className="fas fa-trash"></i>
                                    </DangerButton>
                                </div>
                            ))}
                        </div>

                        <div className="flex justify-between mt-2 space-x-2 text-end">
                            <SecondaryButton
                                onClick={() => {
                                    setShowMenuModal(false);
                                    router.get(route("system.navigations.index"));
                                }}
                            >
                                close
                            </SecondaryButton>

                            <div>
                                <PrimaryButton className="mr-1" type="button" onClick={addNewMenuItem}>
                                    <i className="fas fa-plus"></i>
                                </PrimaryButton>
                                <PrimaryButton type="button" onClick={updateMenuItems}>
                                    update
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
