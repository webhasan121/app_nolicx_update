import { Head, useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import Image from "../../../../components/Image";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import { router } from "@inertiajs/react";

export default function Slides({ id, slides = [] }) {
    const [showSlides, setShowSlides] = useState(true);
    const [showCreateModal, setShowCreateModal] = useState(false);
    const form = useForm({
        image: null,
        url: "",
    });

    const previewUrl = useMemo(() => {
        if (form.data.image instanceof File) {
            return URL.createObjectURL(form.data.image);
        }

        return null;
    }, [form.data.image]);

    const submit = () => {
        form.post(route("system.static-slider.slides.store", { slider: id }), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                setShowCreateModal(false);
                form.reset();
            },
        });
    };

    const destroySlide = (slideId) => {
        if (!window.confirm("Are your sure want to delete ?")) {
            return;
        }

        router.delete(route("system.static-slider.slides.destroy", { slide: slideId }));
    };

    return (
        <AppLayout
            title={`Static Slider-Slides #${id}`}
            header={<PageHeader>{`Static Slider-Slides #${id}`}</PageHeader>}
        >
            <Head title={`Static Slider-Slides #${id}`} />

            <Container>
                <Section>
                    <div className="flex items-center justify-between py-2">
                        <div className="flex items-center space-x-2">
                            <p>Slides</p>
                            <PrimaryButton onClick={() => setShowCreateModal(true)}>
                                <i className="mr-2 fas fa-plus"></i> Image
                            </PrimaryButton>
                        </div>
                        <div onClick={() => setShowSlides((current) => !current)}>
                            <i className="fas fa-angle-down"></i>
                        </div>
                    </div>
                    <Hr />

                    <div className="py-3">
                        {showSlides ? (
                            <div className="p-3">
                                <Hr />
                                {slides.map((item) => (
                                    <div className="relative p-1 mb-2 rounded-md shadow" key={item.id}>
                                        <Image
                                            src={`/storage/${item.image}`}
                                            className="w-full rounded-md"
                                            alt=""
                                        />

                                        <div className="absolute bottom-0 left-0 w-full p-3 bg-gray-100/50">
                                            <div className=" p-3">
                                                <div className="mb-1">
                                                    <p className="text-xs font-bold"> Action URL</p>
                                                    <p className="text-sm text-gray-600">{item.action_url ?? "N\\A"}</p>
                                                </div>

                                                <div
                                                    className="inline-block px-3 py-1 text-xs text-white bg-red-400 rounded-md shadow-md hover:bg-red-700"
                                                    onClick={() => destroySlide(item.id)}
                                                >
                                                    <i className="mr-2 fas fa-trash"></i> Erase
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : null}
                    </div>
                </Section>
            </Container>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)}>
                <div className="p-3">
                    <div className="flex items-center justify-between">
                        <div>Add Slides Image</div>
                        <div onClick={() => setShowCreateModal(false)}>
                            <i className="fas fa-times"></i>
                        </div>
                    </div>
                </div>
                <Hr />
                <div className="p-3">
                    {previewUrl ? <Image src={previewUrl} className="w-full rounded shadow" alt="" /> : null}
                    <br />
                    <InputLabel>Slide Image</InputLabel>
                    <div className="relative">
                        <label htmlFor="slides" className="fas fa-upload"></label>
                        <TextInput
                            type="file"
                            onChange={(e) => form.setData("image", e.target.files?.[0] ?? null)}
                            className="absolute hidden"
                            id="slides"
                        />
                        {form.errors.image ? <p className="text-red-400">{form.errors.image}</p> : null}
                    </div>
                    <br />
                    <div>
                        <InputLabel>Action URL</InputLabel>
                        <TextInput
                            type="text"
                            placeholder="action url"
                            value={form.data.url}
                            onChange={(e) => form.setData("url", e.target.value)}
                            className="w-full"
                        />
                    </div>
                </div>
                <Hr />
                <div className="flex items-center justify-end p-3 space-x-2 text-end">
                    <SecondaryButton type="button" onClick={() => setShowCreateModal(false)}>
                        <i className="mr-2 fas fa-times"></i>
                        close
                    </SecondaryButton>
                    <PrimaryButton type="button" onClick={submit}>
                        <i className="mr-2 fas fa-file"></i> Save
                    </PrimaryButton>
                </div>
            </Modal>
        </AppLayout>
    );
}
