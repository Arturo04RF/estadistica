{**
 * frontend/publicationsStats.tpl
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Frontend page displaying publication statistics.
 *
 *}
{extends file="layouts/frontend.tpl"}

{block name="page"}

    <div class="frontendStats">
        <pkp-header>
            <h1>{translate key="common.publications"}</h1>
            <spinner v-if="isLoadingTimeline"></spinner>
            <template slot="actions">
                <date-range
                    unique-id="frontend-stats-date-range"
                    :date-start="dateStart"
                    :date-start-min="dateStartMin"
                    :date-end="dateEnd"
                    :date-end-max="dateEndMax"
                    :options="dateRangeOptions"
                    date-range-label="{translate key="stats.dateRange"}"
                    date-format-instructions-label="{translate key="stats.dateRange.instructions"}"
                    change-date-range-label="{translate key="stats.dateRange.change"}"
                    since-date-label="{translate key="stats.dateRange.sinceDate"}"
                    until-date-label="{translate key="stats.dateRange.untilDate"}"
                    all-dates-label="{translate key="stats.dateRange.allDates"}"
                    custom-range-label="{translate key="stats.dateRange.customRange"}"
                    from-date-label="{translate key="stats.dateRange.from"}"
                    to-date-label="{translate key="stats.dateRange.to"}"
                    apply-label="{translate key="stats.dateRange.apply"}"
                    invalid-date-label="{translate key="stats.dateRange.invalidDate"}"
                    date-does-not-exist-label="{translate key="stats.dateRange.dateDoesNotExist"}"
                    invalid-date-range-label="{translate key="stats.dateRange.invalidDateRange"}"
                    invalid-end-date-max-label="{translate key="stats.dateRange.invalidEndDateMax"}"
                    invalid-start-date-min-label="{translate key="stats.dateRange.invalidStartDateMin"}"
                    @set-range="setDateRange"
                ></date-range>
            </template>
        </pkp-header>
        <div class="frontendStats__container -pkpClearfix">
            <div v-if="chartData" class="frontendStats__graph">
                <div class="frontendStats__graphHeader">
                    <h2 class="frontendStats__graphTitle -screenReader" id="publication-stats-graph-title">
                        {translate key="submission.views"}
                    </h2>
                    <div class="frontendStats__graphSelectors">
                        <div class="frontendStats__graphSelector frontendStats__graphSelector--timelineType">
                            <pkp-button
                                :aria-pressed="timelineType === 'abstract'"
                                aria-describedby="publication-stats-graph-title"
                                @click="setTimelineType('abstract')"
                            >
                                {translate key="stats.publications.abstracts"}
                            </pkp-button>
                            <pkp-button
                                :aria-pressed="timelineType === 'galley'"
                                aria-describedby="publication-stats-graph-title"
                                @click="setTimelineType('galley')"
                            >
                                {translate key="submission.files"}
                            </pkp-button>
                        </div>
                        <div class="frontendStats__graphSelector frontendStats__graphSelector--timelineInterval">
                            <pkp-button
                                :aria-pressed="timelineInterval === 'day'"
                                aria-describedby="publication-stats-graph-title"
                                :disabled="!isDailyIntervalEnabled"
                                @click="setTimelineInterval('day')"
                            >
                                {translate key="stats.daily"}
                            </pkp-button>
                            <pkp-button
                                :aria-pressed="timelineInterval === 'month'"
                                aria-describedby="publication-stats-graph-title"
                                :disabled="!isMonthlyIntervalEnabled"
                                @click="setTimelineInterval('month')"
                            >
                                {translate key="stats.monthly"}
                            </pkp-button>
                        </div>
                    </div>
                </div>
                <line-chart :chart-data="chartData" aria-hidden="true"></line-chart>
                <span v-if="isLoadingTimeline" class="frontendStats__loadingCover">
                    <spinner></spinner>
                </span>
            </div>
        </div>
    </div>

{/block}

