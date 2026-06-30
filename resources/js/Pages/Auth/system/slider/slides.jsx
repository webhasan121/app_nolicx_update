import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import InputLabel from "../../../../components/InputLabel";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function Slides({ slider, slides = [] }) {
    const [forms, setForms] = useState([]);
    const [previews, setPreviews] = useState({});

    useEffect(() => {
        setForms(
            slides.map((item) => ({
                ...item,
                imageFile: null,
            }))
        );
        setPreviews({});
    }, [slides]);

    const addNewSlides = () => {
        router.post(route("system.slider.slides.add", { slider: slider.id }));
    };

    const updateSlideField = (index, field, value) => {
        setForms((items) =>
            items.map((item, key) =>
                key === index ? { ...item, [field]: value } : item
            )
        );
    };

    const updateImage = (index, file) => {
        setForms((items) =>
            items.map((item, key) =>
                key === index ? { ...item, imageFile: file } : item
            )
        );

        if (file) {
            const url = URL.createObjectURL(file);
            setPreviews((items) => ({ ...items, [index]: url }));
        }
    };

    const saveSlide = (index) => {
        const item = forms[index];

        const payload = {
            main_title: item.main_title ?? "",
            description: item.description ?? "",
            action_text: item.action_text ?? "",
            action_url: item.action_url ?? "",
            action_target: item.action_target ?? "",
            title_color: item.title_color ?? "",
            des_color: item.des_color ?? "",
            image: item.imageFile ?? null,
        };

        router.post(route("system.slider.slides.update", { slide: item.id }), payload, {
            forceFormData: true,
        });
    };

    const deleteSlide = (item) => {
        if (!window.confirm("Are you sure you want to delete this slide?")) {
            return;
        }

        router.delete(route("system.slider.slides.destroy", { slide: item.id }));
    };

    return (
        <AppLayout title={`Slider- ${slider?.name ?? ""}`}>
            <Head title={`Slider- ${slider?.name ?? ""}`} />

            <div className="w-full px-2 mx-auto max-w-8xl sm:px-6 lg:px-8">
                <PageHeader>{`Slider- ${slider?.name ?? ""}`}</PageHeader>
            </div>

            <Container>
                <Section>
                    <SectionInner>
                        <div className="w-full">
                            {forms.map((item, key) => (
                                <div key={item.id} className="relative w-full p-3 mb-1 border rounded">
                                    <div className="grid w-full items-start gap-4 p-3 xl:grid-cols-[280px_minmax(0,1fr)]">
                                        <div className="p-2">
                                            {previews[key] ? (
                                                <img
                                                    src={previews[key]}
                                                    className="h-[150px] w-full rounded object-cover"
                                                    alt=""
                                                />
                                            ) : (
                                                <img
                                                    src={`/storage/${item.image}`}
                                                    className="h-[150px] w-full rounded object-cover"
                                                    alt=""
                                                />
                                            )}

                                            <div className="relative mt-3">
                                                <input
                                                    type="file"
                                                    id={`slider_image_${key}`}
                                                    accept="jpg, jpeg, png"
                                                    max="500"
                                                    className="absolute hidden w-full p-1 border"
                                                    onChange={(e) =>
                                                        updateImage(key, e.target.files?.[0] ?? null)
                                                    }
                                                />
                                                <label
                                                    htmlFor={`slider_image_${key}`}
                                                    className="inline-flex items-center justify-center p-2 border rounded shadow"
                                                >
                                                    <i className="px-1 fas fa-upload"></i>
                                                </label>
                                            </div>

                                            <div className="flex items-center justify-between py-2 my-3 border-y">
                                                <span className="text-sm font-medium">Background Color</span>
                                                <input
                                                    type="color"
                                                    className="w-8 h-8 rounded shadow"
                                                    value={item.action_target ?? ""}
                                                    onChange={(e) =>
                                                        updateSlideField(key, "action_target", e.target.value)
                                                    }
                                                />
                                            </div>
                                        </div>
                                        <div className="min-w-0 p-2 space-y-2">
                                            <p className="flex items-center justify-between text-xs text-end">
                                                Title
                                                <input
                                                    type="color"
                                                    className="w-8 h-4 mb-1 rounded"
                                                    value={item.title_color ?? ""}
                                                    onChange={(e) =>
                                                        updateSlideField(key, "title_color", e.target.value)
                                                    }
                                                />
                                            </p>
                                            <textarea
                                                rows="3"
                                                value={item.main_title ?? ""}
                                                onChange={(e) =>
                                                    updateSlideField(key, "main_title", e.target.value)
                                                }
                                                className="w-full border border-gray-600 rounded"
                                                placeholder="Main Title"
                                            ></textarea>
                                            <p className="flex items-center justify-between text-xs text-end">
                                                Des
                                                <input
                                                    type="color"
                                                    className="w-8 h-4 mb-1 rounded"
                                                    value={item.des_color ?? ""}
                                                    onChange={(e) =>
                                                        updateSlideField(key, "des_color", e.target.value)
                                                    }
                                                />
                                            </p>
                                            <textarea
                                                value={item.description ?? ""}
                                                onChange={(e) =>
                                                    updateSlideField(key, "description", e.target.value)
                                                }
                                                className="w-full border border-gray-600 rounded"
                                                rows="3"
                                                placeholder="Description"
                                            ></textarea>

                                            <hr className="my-2" />
                                            <p className="text-xs">Action Button</p>
                                            <TextInput
                                                type="text"
                                                value={item.action_text ?? ""}
                                                onChange={(e) =>
                                                    updateSlideField(key, "action_text", e.target.value)
                                                }
                                                className="w-full"
                                                placeholder="Active Text"
                                            />
                                            <TextInput
                                                type="text"
                                                value={item.action_url ?? ""}
                                                onChange={(e) =>
                                                    updateSlideField(key, "action_url", e.target.value)
                                                }
                                                className="w-full"
                                                placeholder="Active URL"
                                            />
                                        </div>
                                    </div>

                                    <Hr />
                                    <div className="flex flex-wrap items-center gap-2">
                                        <DangerButton className="inline-flex justify-center w-auto" onClick={() => deleteSlide(item)}>
                                            <i className="fas fa-trash"></i>
                                        </DangerButton>
                                        <PrimaryButton className="inline-flex justify-center w-auto" onClick={() => saveSlide(key)}>
                                            <i className="pr-2 fas fa-save "></i> save
                                        </PrimaryButton>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className="flex sm:justify-end">
                            <PrimaryButton className="inline-flex justify-center w-auto" onClick={addNewSlides}>
                                <i className="pr-2 fas fa-plus"></i> Slides
                            </PrimaryButton>
                        </div>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
