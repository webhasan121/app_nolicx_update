import Image from "./Image";

export default function ImageTemp({ model, temp, src }) {
  const tempSrc =
    typeof temp === "string"
      ? temp
      : temp?.temporaryUrl
        ? temp.temporaryUrl()
        : temp?.preview
          ? temp.preview
          : null;

  return (
    <div>
      <div className="flex">
        {model && !temp && <Image className="mb-2" style={{ width: "150px", height: "100px" }} src={src} alt="IMAGE" />}
        {tempSrc && (
          <img
            className="mb-2"
            style={{ width: "150px", height: "100px" }}
            src={tempSrc}
            alt="IMAGE"
          />
        )}
      </div>
    </div>
  );
}

