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
        router.delete(route("system.slider.slides.destroy", { slide: item.id }));
    };

    return (
        <AppLayout title={`Slider- ${slider?.name ?? ""}`}>
            <Head title={`Slider- ${slider?.name ?? ""}`} />

            <PageHeader>{`Slider- ${slider?.name ?? ""}`}</PageHeader>

            <Container>
                <Section>
                    <SectionInner>
                        <div className="w-full">
                            {forms.map((item, key) => (
                                <div key={item.id} className="relative w-full p-3 mb-1 border rounded">
                                    <div className="items-start w-full p-3 md:flex jusitfy-between">
                                        <div className="p-2">
                                            {previews[key] ? (
                                                <img
                                                    src={previews[key]}
                                                    style={{ height: 150, width: "100%" }}
                                                    alt=""
                                                />
                                            ) : (
                                                <img
                                                    src={`/storage/${item.image}`}
                                                    style={{ height: 150, width: "100%" }}
                                                    alt=""
                                                />
                                            )}

                                            <div className="relative">
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
                                                    className="p-1 border rounded shadow"
                                                >
                                                    <i className="px-1 fas fa-upload"></i>
                                                </label>
                                            </div>
                                            <br />

                                            <div className="flex items-center justify-between py-2 my-2 border-t border-b">
                                                Background Color
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
                                        <div className="p-2 space-y-2">
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
                                    <div className="flex items-center justify-start space-x-2">
                                        <DangerButton onClick={() => deleteSlide(item)}>
                                            <i className="fas fa-trash"></i>
                                        </DangerButton>
                                        <PrimaryButton onClick={() => saveSlide(key)}>
                                            <i className="pr-2 fas fa-save "></i> save
                                        </PrimaryButton>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className="flex justify-end space-x-2">
                            <PrimaryButton onClick={addNewSlides}>
                                <i className="pr-2 fas fa-plus"></i> Slides
                            </PrimaryButton>
                        </div>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
