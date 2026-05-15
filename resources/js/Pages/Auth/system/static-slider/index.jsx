import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
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
import Slider from "../../../../livewire/system/static-slider/Slider";
import { useState } from "react";
import useTranslation from "../../../../hooks/useTranslation";

function checkboxRow(id, checked, onChange, label, text, border = "border-b") {
    return (
        <div className={`flex justify-start items-start my-2 ${border} py-2`}>
            <input
                type="checkbox"
                id={id}
                checked={checked}
                onChange={onChange}
                style={{ width: "20px", height: "20px" }}
                className="me-3"
            />
            <div>
                <InputLabel className="py-0 my-0" htmlFor={id}>
                    {label}
                </InputLabel>
                <p className="text-xs">{text}</p>
            </div>
        </div>
    );
}

export default function Index({ slider = [] }) {
    const { t } = useTranslation();
    const [showCreateModal, setShowCreateModal] = useState(false);
    const form = useForm({
        sliderName: "",
        home: false,
        about: false,
        order: false,
        product_details: false,
        categories_product: false,
        top: false,
        middle: false,
        bottom: false,
        status: false,
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route("system.static-slider.store"), {
            preserveScroll: true,
            onSuccess: () => {
                setShowCreateModal(false);
                form.reset();
            },
        });
    };



    return (
        <AppLayout
            title={t("Static Slider")}
            header={<PageHeader>{t("Static Slider")}</PageHeader>}
        >
            <Head title={t("Static Slider")} />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <PrimaryButton onClick={() => setShowCreateModal(true)}>
                                    <i className="pr-2 fas fa-plus"></i>{t("Add")}</PrimaryButton>
                            </div>
                        }
                        content=""
                    />

                    <SectionInner>
                        {slider.map((item, key) => (
                            <Slider item={item} index={key + 1} key={item.id} />
                        ))}
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)}>
                <div className="p-3">{t("Slider Modal")}</div>
                <hr />
                <div className="p-3">
                    <strong></strong>
                    <form onSubmit={submit}>
                        <div className="flex">
                            <TextInput
                                value={form.data.sliderName}
                                onChange={(e) => form.setData("sliderName", e.target.value)}
                                className="w-full py-1 rounded-0"
                                placeholder={t("Give Slider Name")}
                            />
                        </div>
                        {form.errors.sliderName ? (
                            <span className="text-xs text-red-900">{form.errors.sliderName}</span>
                        ) : null}

                        <div className="items-start justify-between p-2 lg:flex">
                            <div className="p-3">
                                {checkboxRow(
                                    "home_page",
                                    form.data.home,
                                    (e) => form.setData("home", e.target.checked),
                                    "Home Page",
                                    <>
                                        If checked, Banner will display on <strong>{t("Home Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "about_page",
                                    form.data.about,
                                    (e) => form.setData("about", e.target.checked),
                                    "About Page",
                                    <>
                                        If checked, Banner will display on <strong>{t("About-Us Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "order_page",
                                    form.data.order,
                                    (e) => form.setData("order", e.target.checked),
                                    "Order Page",
                                    <>
                                        If checked, Banner will display on <strong>{t("Order Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "product_details_page",
                                    form.data.product_details,
                                    (e) => form.setData("product_details", e.target.checked),
                                    "Product Details Page",
                                    <>
                                        If checked, Banner will display on <strong>{t("Product Details Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "categories_product_page",
                                    form.data.categories_product,
                                    (e) => form.setData("categories_product", e.target.checked),
                                    "Categories Product Page",
                                    <>
                                        If checked, Banner will display on <strong>{t("Categories Product Page")}</strong>.
                                    </>
                                )}
                            </div>
                            <br />

                            <div className="p-3 bg-gray-100">
                                {checkboxRow(
                                    "page_top",
                                    form.data.top,
                                    (e) => form.setData("top", e.target.checked),
                                    "Top",
                                    <>
                                        If checked, Banner will display on <strong>{t("Top Of The Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "page_middle",
                                    form.data.middle,
                                    (e) => form.setData("middle", e.target.checked),
                                    "Middle",
                                    <>
                                        If checked, Banner will display on <strong>{t("Middle Of The Page")}</strong>.
                                    </>
                                )}
                                {checkboxRow(
                                    "page_bottom",
                                    form.data.bottom,
                                    (e) => form.setData("bottom", e.target.checked),
                                    "Bottom",
                                    <>
                                        If checked, Banner will display on <strong>{t("Bottom Of The Page")}</strong>.
                                    </>,
                                    "border-"
                                )}
                            </div>
                        </div>

                        <div className="flex items-start justify-start p-2 my-2 border-b">
                            <input
                                type="checkbox"
                                id="active"
                                checked={form.data.status}
                                onChange={(e) => form.setData("status", e.target.checked)}
                                style={{ width: "20px", height: "20px" }}
                                className="me-3"
                            />
                            <InputLabel className="py-0 my-0" htmlFor="active">
                                Active Now{" "}
                            </InputLabel>
                        </div>
                        {form.errors.status ? (
                            <span className="text-xs text-red-900">{form.errors.status}</span>
                        ) : null}
                        <div className="flex justify-between">
                            <SecondaryButton
                                type="button"
                                className="mt-2"
                                onClick={() => setShowCreateModal(false)}
                            >{t("Cancel")}</SecondaryButton>
                            <PrimaryButton className="mt-2">
                                <i className="pr-2 fas fa-plus"></i>{t("Add")}</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AppLayout>
    );
}
