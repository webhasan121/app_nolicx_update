import InputFile from "../../../../components/InputFile";
import InputLabel from "../../../../components/InputLabel";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Hr from "../../../../components/Hr";

export default function UpdateProfileInformation({
    editUser,
    defaultAdminRef,
    profileForm,
    onSubmit,
}) {
    return (
        <form onSubmit={onSubmit}>
            <div className=" m-0">
                <InputFile label="User Name" error="name" name="name">
                    <TextInput
                        type="text"
                        className="w-full"
                        value={profileForm.data.name}
                        onChange={(e) =>
                            profileForm.setData("name", e.target.value)
                        }
                    />
                </InputFile>
                <Hr />
                <InputFile label="User Email" error="email" name="email">
                    <TextInput
                        type="text"
                        className="w-full"
                        value={profileForm.data.email}
                        onChange={(e) =>
                            profileForm.setData("email", e.target.value)
                        }
                    />
                </InputFile>
                <Hr />
                <InputFile
                    label="User Reference"
                    error="reference"
                    name="reference"
                >
                    {editUser?.reference ? (
                        <div>
                            Accept ref by{" "}
                            <strong>
                                {editUser?.reference_owner_name ?? "Not Found"}
                            </strong>
                        </div>
                    ) : null}
                    <TextInput
                        disabled
                        type="text"
                        className="w-full"
                        value={profileForm.data.reference}
                    />
                    <div className="p-2 rounded border border-slate-600 mt-3 shadow-sm">
                        <div className=" items-center my-2 border p-2 rounded ">
                            <InputLabel forInput="new_ref">
                                Custom Ref
                            </InputLabel>
                            <TextInput
                                type="text"
                                placeholder="Write custom referred code"
                                id="new_ref"
                                value={profileForm.data.cref}
                                onChange={(e) =>
                                    profileForm.setData("cref", e.target.value)
                                }
                            />
                        </div>
                        <hr />
                        <div className="flex items-start my-2">
                            <TextInput
                                type="checkbox"
                                id="reference"
                                checked={
                                    profileForm.data.cref === defaultAdminRef
                                }
                                onChange={(e) =>
                                    profileForm.setData(
                                        "cref",
                                        e.target.checked ? defaultAdminRef : ""
                                    )
                                }
                                style={{
                                    width: 25,
                                    height: 25,
                                    marginRight: 25,
                                }}
                            />
                            <div>
                                <p className="bold font-bold fw-bold m-0">
                                    Set Default Admin Ref
                                </p>
                                <h6>
                                    In case of set the admin default ref, please
                                    check the box.
                                </h6>
                            </div>
                        </div>
                    </div>
                    <Hr />
                    <PrimaryButton>Update User</PrimaryButton>
                </InputFile>
            </div>
        </form>
    );
}
