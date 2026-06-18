import { useEffect, useMemo, useState } from "react";
import InputFile from "./InputFile";
import SearchableSelect from "./SearchableSelect";

export default function DistrictUpozilaSelect({
    district = "",
    upozila = "",
    onDistrictChange,
    onUpozilaChange,
    errors = {},
    states: initialStates,
    districtLabel = "District",
    upozilaLabel = "Upozila",
    districtName = "district",
    upozilaName = "upozila",
    labelWidth,
    className = "md:flex",
}) {
    const providedStates = useMemo(() => initialStates ?? [], [initialStates]);
    const [states, setStates] = useState(providedStates);
    const [cities, setCities] = useState([]);

    useEffect(() => {
        if (providedStates.length) {
            setStates(providedStates);
            return;
        }

        let ignore = false;

        axios
            .get("/api/countries", {
                params: {
                    search: "Bangladesh",
                    per_page: 1,
                },
            })
            .then((response) => {
                const countryId = response.data?.data?.[0]?.id;

                if (!countryId) {
                    return [];
                }

                return axios.get("/api/states", {
                    params: {
                        country_id: countryId,
                        per_page: 100,
                    },
                });
            })
            .then((response) => {
                if (!ignore && response?.data?.data) {
                    setStates(response.data.data);
                }
            })
            .catch(() => {
                if (!ignore) {
                    setStates([]);
                }
            });

        return () => {
            ignore = true;
        };
    }, [providedStates]);

    const selectedState = useMemo(
        () =>
            states.find(
                (state) =>
                    String(state.id) === String(district) ||
                    String(state.name).toLowerCase() === String(district).toLowerCase(),
            ),
        [district, states],
    );

    useEffect(() => {
        if (!selectedState?.id) {
            setCities([]);
            return;
        }

        let ignore = false;

        axios
            .get("/api/cities", {
                params: {
                    state_id: selectedState.id,
                    per_page: 100,
                },
            })
            .then((response) => {
                if (!ignore) {
                    setCities(response.data?.data || []);
                }
            })
            .catch(() => {
                if (!ignore) {
                    setCities([]);
                }
            });

        return () => {
            ignore = true;
        };
    }, [selectedState?.id]);

    return (
        <>
            <InputFile
                className={className}
                labelWidth={labelWidth}
                label={districtLabel}
                name={districtName}
                error={districtName}
                errors={errors}
                inputClass="w-full"
            >
                <SearchableSelect
                    value={district}
                    options={states}
                    valueKey="name"
                    className="w-full"
                    inputClassName="w-full"
                    onChange={(selectedDistrict) => {
                        onDistrictChange?.(selectedDistrict);
                        onUpozilaChange?.("");
                    }}
                    placeholder="-- Select District --"
                    noneLabel="-- Select District --"
                    noResultsLabel="No district found."
                />
            </InputFile>

            <InputFile
                className={className}
                labelWidth={labelWidth}
                label={upozilaLabel}
                name={upozilaName}
                error={upozilaName}
                errors={errors}
                inputClass="w-full"
            >
                <SearchableSelect
                    value={upozila}
                    options={cities}
                    valueKey="name"
                    className="w-full"
                    inputClassName="w-full"
                    onChange={(selectedUpozila) => onUpozilaChange?.(selectedUpozila)}
                    placeholder="-- Select Upozila --"
                    noneLabel="-- Select Upozila --"
                    noResultsLabel="No upozila found."
                    disabled={!selectedState}
                />
            </InputFile>
        </>
    );
}
